<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitationHost>
 */
class InvitationHostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'type' => 'host',
            'name' => fake()->name(),
            'nickname' => fake()->firstName(),
            'photo_path' => null,
            'parent_name' => fake()->name(),
            'birth_order' => 'Putra Pertama',
            'description' => null,
            'sort_order' => 0,
        ];
    }
}
