<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tour;
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

            $factory->count(self::TOURS_PER_CATEGORY)->create();
        }

        // 1 tour ngừng bán để test bộ lọc trạng thái
        Tour::factory()->forCategory($categories->first())->inactive()->create();
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
