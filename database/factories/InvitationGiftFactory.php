<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitationGift>
 */
class InvitationGiftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'type' => 'bank',
            'bank_name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI', 'GoPay', 'OVO']),
            'account_number' => fake()->numerify('##########'),
            'account_name' => fake()->name(),
            'sort_order' => 0,
        ];
    }
}
