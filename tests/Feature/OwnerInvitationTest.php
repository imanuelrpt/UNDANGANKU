<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OwnerInvitationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_can_create_draft_invitation_and_opens_wizard(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('invitations.store'), [
            'title' => 'Undangan Demo',
            'event_type' => 'wedding',
            'slug' => 'undangan-demo',
            'theme' => 'elegant-gold',
        ]);

        $invitation = Invitation::where('slug', 'undangan-demo')->firstOrFail();
        $response->assertRedirectToRoute('invitations.edit', $invitation);
        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'user_id' => $owner->id,
            'status' => 'draft',
        ]);
    }

    public function test_owner_can_publish_own_invitation(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create([
            'status' => InvitationStatus::Draft,
        ]);

        $response = $this->actingAs($owner)->post(route('invitations.publish', $invitation));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Undangan sudah dipublikasikan.');
        $this->assertSame(InvitationStatus::Published, $invitation->fresh()->status);
    }

    public function test_owner_cannot_edit_another_owners_invitation(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $invitation = Invitation::factory()->for($otherOwner)->create();

        $response = $this->actingAs($owner)->get(route('invitations.edit', $invitation));

        $response->assertForbidden();
    }
}
