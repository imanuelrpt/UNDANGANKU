<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use App\Services\InvitationThemeResolver;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_is_forbidden_from_admin_panel(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->get(route('admin.users'));

        $response->assertForbidden();
    }

    public function test_admin_can_deactivate_another_user_and_log_the_action(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle', $target));

        $response->assertRedirect();
        $this->assertFalse($target->fresh()->is_active);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'user.toggled',
        ]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.toggle', $admin));

        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_disabling_theme_makes_resolver_fall_back_to_default(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.themes.update', 'minimal-modern'), [
            'theme' => 'minimal-modern',
            'active' => false,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Status tema berhasil diperbarui.');
        $invitation = Invitation::factory()->make(['theme' => 'minimal-modern']);
        $resolved = app(InvitationThemeResolver::class)->resolve($invitation);
        $this->assertSame('elegant-gold', $resolved['key']);
    }
}
