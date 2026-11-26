<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class InvitationPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_can_manage_own_invitation(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        $allowed = Gate::forUser($owner)->allows('update', $invitation);

        $this->assertSame(true, $allowed);
    }

    public function test_owner_cannot_manage_another_owners_invitation(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $invitation = Invitation::factory()->for($otherOwner)->create();

        $allowed = Gate::forUser($owner)->allows('update', $invitation);

        $this->assertSame(false, $allowed);
    }

    public function test_admin_can_manage_any_invitation(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        $allowed = Gate::forUser($admin)->allows('delete', $invitation);

        $this->assertSame(true, $allowed);
    }
}
