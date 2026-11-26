<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PublicInvitationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_published_invitation_renders_selected_theme_and_counts_view(): void
    {
        $invitation = Invitation::factory()->published()->create([
            'title' => 'Rina & Dimas',
            'slug' => 'rina-dan-dimas',
            'theme' => 'rustic-green',
        ]);

        $response = $this->get(route('invitation.public', $invitation->slug));

        $response->assertOk()
            ->assertViewIs('themes.rustic-green.show')
            ->assertSee('Rina & Dimas');
        $this->assertSame(1, $invitation->fresh()->view_count);
    }

    public function test_draft_invitation_returns_404_and_is_not_counted(): void
    {
        $invitation = Invitation::factory()->create(['slug' => 'undangan-draft']);

        $response = $this->get(route('invitation.public', $invitation->slug));

        $response->assertNotFound();
        $this->assertSame(0, $invitation->fresh()->view_count);
    }

    public function test_personal_link_marks_guest_as_opened_and_shows_their_name(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        $guest = Guest::factory()->for($invitation)->create([
            'name' => 'Dewi Anggraini',
            'token' => 'tamu-demo-token',
        ]);

        $response = $this->get(route('invitation.public.guest', [$invitation->slug, $guest->token]));

        $response->assertOk()->assertSee('Dewi Anggraini');
        $this->assertNotNull($guest->fresh()->opened_at);
        $this->assertSame(1, $guest->fresh()->open_count);
    }

    public function test_public_page_escapes_wishes_and_hides_hidden_wishes(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        Wish::factory()->for($invitation)->create([
            'name' => 'Tamu Biasa',
            'message' => '<script>alert(1)</script> Selamat!',
        ]);
        Wish::factory()->for($invitation)->hidden()->create([
            'message' => 'Ucapan yang disembunyikan',
        ]);

        $response = $this->get(route('invitation.public', $invitation->slug));

        $response->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('Ucapan yang disembunyikan');
    }
}
