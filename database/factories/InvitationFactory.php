<?php

namespace Database\Factories;

use App\Enums\EventType;
use App\Enums\InvitationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => EventType::Wedding,
            'title' => fake()->sentence(3),
            'slug' => Str::slug(fake()->unique()->sentence(3)),
            'theme' => 'elegant-gold',
            'status' => InvitationStatus::Draft,
            'expires_at' => null,
            'settings' => [],
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => InvitationStatus::Published]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => InvitationStatus::Archived]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => ['expires_at' => now()->subDay()]);
    }

    public function theme(string $theme): static
    {
        return $this->state(fn (array $attributes): array => ['theme' => $theme]);
    }
}
