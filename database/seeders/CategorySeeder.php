<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Danh mục gốc (type) => danh sách danh mục con.
     *
     * @var array<string, list<string>>
     */
    public const TREE = [
        'Tour' => [
            'Tour trong nước',
            'Tour nước ngoài',
            'Tour biển đảo',
            'Tour khám phá',
            'Tour nghỉ dưỡng',
        ],
        'Place' => [
            'Miền Bắc',
            'Miền Trung',
            'Miền Nam',
        ],
        'Food' => [
            'Ẩm thực đường phố',
            'Đặc sản vùng miền',
        ],
        'News' => [
            'Tin khuyến mãi',
            'Cẩm nang du lịch',
        ],
    ];

    public function run(): void
    {
        foreach (self::TREE as $root => $children) {
            $parent = Category::firstOrCreate([
                'parent_id' => null,
                'name' => $root,
            ]);

            foreach ($children as $child) {
                Category::firstOrCreate([
                    'parent_id' => $parent->category_id,
                    'name' => $child,
                ]);
            }
        }
    }
}
