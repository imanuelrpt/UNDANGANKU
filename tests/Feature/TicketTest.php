<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Services\QrTicketService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_signed_ticket_verifies_expected_guest_identifiers(): void
    {
        $guest = Guest::factory()->create(['token' => 'tamu-qr']);
        $ticketService = app(QrTicketService::class);

        $signed = $ticketService->sign($guest);

        $this->assertSame([
            'i' => $guest->invitation_id,
            't' => 'tamu-qr',
        ], $ticketService->verify($signed));
        $this->assertNull($ticketService->verify($signed.'dimanipulasi'));
    }

    public function test_ticket_image_returns_svg_without_cache(): void
    {
        $guest = Guest::factory()->create(['token' => 'tamu-qr']);

        $response = $this->get(route('ticket.image', $guest->token));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertStringContainsString('<svg', (string) $response->getContent());
    }

    public function test_ticket_image_returns_404_for_unknown_token(): void
    {
        $response = $this->get(route('ticket.image', 'token-tidak-ada'));

        $response->assertNotFound();
    }
}
