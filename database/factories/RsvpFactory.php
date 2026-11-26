<?php

namespace Database\Factories;

use App\Enums\Attendance;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rsvp>
 */
class RsvpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_id' => null,
            'name' => fake()->name(),
            'attendance' => fake()->randomElement(Attendance::cases()),
            'pax' => fake()->numberBetween(1, 3),
            'answers' => [],
            'note' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function attending(): static
    {
        return $this->state(fn (array $attributes): array => ['attendance' => Attendance::Hadir]);
    }

    public function notAttending(): static
    {
        return $this->state(fn (array $attributes): array => ['attendance' => Attendance::TidakHadir]);
    }
}
