<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourSchedule>
 */
class TourScheduleFactory extends Factory
{
    protected $model = TourSchedule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => Tour::factory(),
            'departure_date' => fake()->unique()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'available_slots' => fake()->numberBetween(10, 40),
            'price_override' => null,
        ];
    }

    /**
     * Set a custom price override.
     */
    public function withPriceOverride(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price_override' => $price,
        ]);
    }
}
