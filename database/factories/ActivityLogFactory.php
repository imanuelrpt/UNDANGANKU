<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'invitation_id' => Invitation::factory(),
            'action' => fake()->randomElement(['invitation.created', 'invitation.updated', 'guest.imported']),
            'description' => fake()->sentence(6),
            'meta' => [],
            'ip_address' => fake()->ipv4(),
        ];
    }
}
