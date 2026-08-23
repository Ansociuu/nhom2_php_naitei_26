<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\TourSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Số tour demo tạo cho mỗi danh mục con của "Tour".
     */
    public const TOURS_PER_CATEGORY = 3;

    public const FOREIGN_CATEGORY = 'Tour nước ngoài';

    public function run(): void
    {
        $categories = $this->tourCategories();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = $this->tourCategories();
        }

        foreach ($categories as $category) {
            $factory = Tour::factory()->forCategory($category);

            if ($category->name === self::FOREIGN_CATEGORY) {
                $factory = $factory->international();
            }

            $tours = $factory->count(self::TOURS_PER_CATEGORY)->create();
            foreach ($tours as $tour) {
                $this->createItinerariesForTour($tour);
                $this->createSchedulesForTour($tour);
            }
        }

        // 1 tour ngừng bán để test bộ lọc trạng thái
        $inactiveTour = Tour::factory()->forCategory($categories->first())->inactive()->create();
        $this->createItinerariesForTour($inactiveTour);
        $this->createSchedulesForTour($inactiveTour);
    }

    protected function createItinerariesForTour(Tour $tour): void
    {
        for ($day = 1; $day <= $tour->duration_days; $day++) {
            TourItinerary::factory()->create([
                'tour_id' => $tour->tour_id,
                'day_number' => $day,
                'title' => sprintf('Ngày %d: Khám phá điểm đến (Lịch trình mẫu)', $day),
            ]);
        }
    }

    protected function createSchedulesForTour(Tour $tour): void
    {
        $dates = [
            now()->addDays(5)->format('Y-m-d'),
            now()->addDays(12)->format('Y-m-d'),
            now()->addDays(20)->format('Y-m-d'),
        ];

        foreach ($dates as $date) {
            TourSchedule::factory()->create([
                'tour_id' => $tour->tour_id,
                'departure_date' => $date,
                'available_slots' => 20,
            ]);
        }
    }

    /**
     * Các danh mục con của danh mục gốc "Tour".
     *
     * @return Collection<int, Category>
     */
    protected function tourCategories(): Collection
    {
        return Category::query()
            ->whereRelation('parent', 'name', 'Tour')
            ->get();
    }
}
