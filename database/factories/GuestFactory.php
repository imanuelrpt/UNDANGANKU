<?php

namespace Database\Factories;

use App\Enums\GuestGroup;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'name' => fake()->name(),
            'group' => fake()->randomElement(GuestGroup::cases()),
            'phone' => '08'.fake()->numerify('##########'),
            'pax_quota' => fake()->numberBetween(1, 4),
            'token' => Str::random(config('undangan.guest_token_length', 24)),
            'sent_at' => null,
            'opened_at' => null,
            'open_count' => 0,
            'notes' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes): array => ['sent_at' => now()->subDays(2)]);
    }

    public function opened(): static
    {
        return $this->state(fn (array $attributes): array => [
            'opened_at' => now()->subDay(),
            'open_count' => 1,
        ]);
    }
}
