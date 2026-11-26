<?php

namespace Tests\Feature;

use App\Models\Checkin;
use App\Models\CheckinStaffLink;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\QrTicketService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffCheckinTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_wrong_pin_keeps_staff_signed_out(): void
    {
        $link = $this->staffLinkWithPin();

        $response = $this->post(route('staff.login.submit', $link->token), ['pin' => '000000']);

        $response->assertRedirect()
            ->assertSessionHas('error', 'PIN tidak benar. Silakan coba lagi.')
            ->assertSessionMissing('staff.'.$link->token);
    }

    public function test_correct_pin_signs_staff_in_to_scanner(): void
    {
        $link = $this->staffLinkWithPin();

        $response = $this->post(route('staff.login.submit', $link->token), ['pin' => '123456']);

        $response->assertRedirectToRoute('staff.scanner', $link->token)
            ->assertSessionHas('staff.'.$link->token, fn (array $session): bool => $session['link_id'] === $link->id);
    }

    public function test_confirmed_qr_scan_creates_checkin_for_the_invited_guest(): void
    {
        $invitation = Invitation::factory()->published()->create();
        $guest = Guest::factory()->for($invitation)->create(['pax_quota' => 2]);
        $link = CheckinStaffLink::factory()->for($invitation)->create();
        $signed = app(QrTicketService::class)->sign($guest);

        $response = $this->withSession($this->staffSession($link))
            ->postJson(route('staff.scan', $link->token), ['token' => $signed, 'confirm' => true]);

        $response->assertOk()
            ->assertJsonPath('status', 'checked_in')
            ->assertJsonPath('guest.name', $guest->name);
        $this->assertDatabaseHas('checkins', [
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'staff_link_id' => $link->id,
            'method' => 'scan',
            'pax_actual' => 2,
        ]);
    }

    public function test_qr_from_another_invitation_is_rejected_without_checkin(): void
    {
        $invitation = Invitation::factory()->published()->create();
        $link = CheckinStaffLink::factory()->for($invitation)->create();
        $otherGuest = Guest::factory()->create();
        $signed = app(QrTicketService::class)->sign($otherGuest);

        $response = $this->withSession($this->staffSession($link))
            ->postJson(route('staff.scan', $link->token), ['token' => $signed]);

        $response->assertOk()->assertJsonPath('status', 'invalid');
        $this->assertSame(0, Checkin::count());
    }

    public function test_manual_checkin_records_selected_guest(): void
    {
        $invitation = Invitation::factory()->published()->create();
        $guest = Guest::factory()->for($invitation)->create(['pax_quota' => 3]);
        $link = CheckinStaffLink::factory()->for($invitation)->create();

        $response = $this->withSession($this->staffSession($link))
            ->postJson(route('staff.manual', $link->token), [
                'guest_id' => $guest->id,
                'pax_actual' => 3,
                'method' => 'manual',
            ]);

        $response->assertCreated()->assertJsonPath('status', 'checked_in');
        $this->assertDatabaseHas('checkins', [
            'guest_id' => $guest->id,
            'method' => 'manual',
            'pax_actual' => 3,
        ]);
    }

    public function test_walk_in_checkin_records_guest_without_invitation_token(): void
    {
        $invitation = Invitation::factory()->published()->create();
        $link = CheckinStaffLink::factory()->for($invitation)->create();

        $response = $this->withSession($this->staffSession($link))
            ->postJson(route('staff.manual', $link->token), [
                'name' => 'Tamu Walk-in Demo',
                'group' => 'teman',
                'pax_actual' => 2,
            ]);

        $response->assertCreated()->assertJsonPath('status', 'checked_in');
        $this->assertDatabaseHas('checkins', [
            'invitation_id' => $invitation->id,
            'guest_id' => null,
            'name' => 'Tamu Walk-in Demo',
            'method' => 'walk_in',
            'pax_actual' => 2,
        ]);
    }

    private function staffLinkWithPin(): CheckinStaffLink
    {
        return CheckinStaffLink::factory()->for(Invitation::factory())->create([
            'token' => 'staff-demo-pin',
            'pin_hash' => Hash::make('123456'),
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function staffSession(CheckinStaffLink $link): array
    {
        return [
            'staff.'.$link->token => [
                'link_id' => $link->id,
                'name' => $link->label,
                'at' => now()->timestamp,
            ],
        ];
    }
}
