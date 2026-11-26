<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CheckinStaffLink>
 */
class CheckinStaffLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'token' => Str::random(config('undangan.staff_token_length', 32)),
            'pin_hash' => null,
            'label' => 'Petugas '.fake()->firstName(),
            'is_active' => true,
        ];
    }
}
