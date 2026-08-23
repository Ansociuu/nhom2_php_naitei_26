<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourImage>
 */
class TourImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => Tour::factory(),
            'cloudinary_public_id' => 'tours/sample_' . fake()->uuid(),
            'secure_url' => fake()->imageUrl(800, 600, 'travel', true),
            'format' => 'jpg',
            'width' => 800,
            'height' => 600,
            'bytes' => fake()->numberBetween(100000, 500000),
            'is_cover' => false,
            'display_order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Set as cover image.
     */
    public function cover(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cover' => true,
        ]);
    }
}
