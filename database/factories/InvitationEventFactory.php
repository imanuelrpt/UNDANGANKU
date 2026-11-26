<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitationEvent>
 */
class InvitationEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'name' => fake()->randomElement(['Akad', 'Resepsi', 'Ulang Tahun', 'Open House']),
            'starts_at' => now()->addDays(30)->setTime(10, 0),
            'ends_at' => now()->addDays(30)->setTime(14, 0),
            'timezone' => 'WIB',
            'venue' => fake()->company().' Hall',
            'address' => fake()->address(),
            'lat' => -6.2088,
            'lng' => 106.8456,
            'maps_url' => null,
            'sort_order' => 0,
        ];
    }
}
