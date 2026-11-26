<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WishTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_wish_from_guest_token_is_saved_with_profanity_filtered(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        $guest = Guest::factory()->for($invitation)->create([
            'name' => 'Dewi Anggraini',
            'token' => 'tamu-ucapan',
        ]);

        $response = $this->post(route('invitation.wish.store', $invitation->slug), [
            'token' => $guest->token,
            'message' => 'Dasar bego, selamat ya!',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Ucapan Anda berhasil dikirim. Terima kasih!');
        $this->assertDatabaseHas('wishes', [
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'name' => 'Dewi Anggraini',
            'message' => 'Dasar ***, selamat ya!',
        ]);
    }

    public function test_wish_load_endpoint_omits_hidden_wishes(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rina-dan-dimas']);
        Wish::factory()->for($invitation)->create(['message' => 'Ucapan yang tampil']);
        Wish::factory()->for($invitation)->hidden()->create(['message' => 'Ucapan rahasia']);

        $response = $this->getJson(route('invitation.wish.load', $invitation->slug));

        $response->assertOk()
            ->assertJsonCount(1, 'wishes')
            ->assertJsonMissing(['message' => 'Ucapan rahasia']);
    }
}
