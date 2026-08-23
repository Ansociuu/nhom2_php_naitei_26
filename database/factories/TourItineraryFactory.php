<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourItinerary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourItinerary>
 */
class TourItineraryFactory extends Factory
{
    protected $model = TourItinerary::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $day = fake()->numberBetween(1, 5);

        return [
            'tour_id' => Tour::factory(),
            'day_number' => $day,
            'title' => sprintf('Ngày %d: %s - %s', $day, fake()->city(), fake()->city()),
            'description' => fake()->paragraph(3),
        ];
    }
}
