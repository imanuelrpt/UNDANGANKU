<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wish>
 */
class WishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_id' => null,
            'name' => fake()->name(),
            'message' => fake()->sentence(12),
            'is_hidden' => false,
            'reply_message' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes): array => ['is_hidden' => true]);
    }
}
