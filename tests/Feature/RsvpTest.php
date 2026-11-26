<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\Rsvp;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_token_rsvp_creates_record_and_links_selected_event(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        $event = InvitationEvent::factory()->for($invitation)->create();
        $guest = Guest::factory()->for($invitation)->create([
            'token' => 'tamu-rsvp',
            'pax_quota' => 3,
        ]);

        $response = $this->post(route('invitation.rsvp.store', $invitation->slug), [
            'token' => $guest->token,
            'attendance' => 'hadir',
            'pax' => 2,
            'event_ids' => [$event->id],
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Terima kasih! Konfirmasi kehadiran Anda sudah kami terima.');
        $this->assertDatabaseHas('rsvps', [
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'attendance' => 'hadir',
            'pax' => 2,
        ]);
        $rsvp = Rsvp::where('guest_id', $guest->id)->firstOrFail();
        $this->assertDatabaseHas('rsvp_events', [
            'rsvp_id' => $rsvp->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_rsvp_rejects_pax_above_guest_quota(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        $guest = Guest::factory()->for($invitation)->create([
            'token' => 'tamu-rsvp',
            'pax_quota' => 2,
        ]);

        $response = $this->post(route('invitation.rsvp.store', $invitation->slug), [
            'token' => $guest->token,
            'attendance' => 'hadir',
            'pax' => 3,
        ]);

        $response->assertSessionHasErrors([
            'pax' => 'Jumlah orang maksimal 2 orang sesuai kuota yang diberikan.',
        ]);
        $this->assertSame(0, Rsvp::count());
    }

    public function test_rsvp_requires_name_attendance_and_pax_without_guest_token(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);

        $response = $this->post(route('invitation.rsvp.store', $invitation->slug), []);

        $response->assertSessionHasErrors(['name', 'attendance', 'pax']);
        $this->assertSame(0, Rsvp::count());
    }
}
