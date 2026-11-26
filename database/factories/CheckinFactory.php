<?php

namespace Database\Factories;

use App\Enums\CheckinMethod;
use App\Enums\GuestGroup;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checkin>
 */
class CheckinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_id' => null,
            'staff_link_id' => null,
            'name' => fake()->name(),
            'group' => fake()->randomElement(GuestGroup::cases())->value,
            'pax_actual' => fake()->numberBetween(1, 3),
            'method' => CheckinMethod::Scan,
            'device_info' => fake()->userAgent(),
            'checked_in_at' => now()->subMinutes(fake()->numberBetween(5, 180)),
        ];
    }
}
