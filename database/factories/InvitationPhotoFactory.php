<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitationPhoto>
 */
class InvitationPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'path' => 'photos/demo.jpg',
            'thumbnail_path' => 'photos/demo-thumb.jpg',
            'caption' => null,
            'sort_order' => 0,
        ];
    }
}
