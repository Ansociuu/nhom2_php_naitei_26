<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * Điểm đến dùng để sinh tên tour.
     */
    public const DESTINATIONS = [
        'Hạ Long', 'Sapa', 'Đà Nẵng', 'Hội An', 'Huế', 'Nha Trang',
        'Đà Lạt', 'Phú Quốc', 'Côn Đảo', 'Ninh Bình', 'Hà Giang',
        'Mộc Châu', 'Quy Nhơn', 'Cần Thơ', 'Phan Thiết',
    ];

    public const FOREIGN_DESTINATIONS = [
        'Bangkok', 'Singapore', 'Seoul', 'Tokyo', 'Bali',
        'Kuala Lumpur', 'Đài Bắc', 'Phuket', 'Hồng Kông',
    ];

    public const DEPARTURES = ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ'];

    public const HIGHLIGHTS = [
        'Ngắm hoàng hôn trên biển',
        'Khám phá phố cổ về đêm',
        'Trải nghiệm ẩm thực địa phương',
        'Chèo thuyền kayak trên vịnh',
        'Săn mây trên đỉnh núi',
        'Tham quan làng nghề truyền thống',
        'Nghỉ dưỡng tại resort 4 sao',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $destination = fake()->randomElement(self::DESTINATIONS);
        $departure = fake()->randomElement(self::DEPARTURES);
        $days = fake()->numberBetween(2, 7);

        return [
            'category_id' => Category::factory(),
            'title' => sprintf('Tour %s - %s %dN%dĐ', $departure, $destination, $days, $days - 1),
            'description' => fake()->paragraphs(3, true),
            'highlights' => implode("\n", fake()->randomElements(self::HIGHLIGHTS, 3)),
            'departure_location' => $departure,
            // giá tính theo ngày, làm tròn tới 100k
            'price' => fake()->numberBetween(8, 30) * 100000 * $days,
            'duration_days' => $days,
            'included_services' => implode("\n", [
                'Xe du lịch đời mới',
                'Khách sạn tiêu chuẩn 3-4 sao',
                'Các bữa ăn theo chương trình',
                'Hướng dẫn viên suốt tuyến',
                'Vé tham quan theo chương trình',
                'Bảo hiểm du lịch',
            ]),
            'excluded_services' => implode("\n", [
                'Chi phí cá nhân, đồ uống',
                'Tiền tip cho hướng dẫn viên và tài xế',
                'Vé máy bay khứ hồi (nếu có)',
                'Thuế VAT',
            ]),
            'status' => 'active',
        ];
    }

    /**
     * Tour nước ngoài: điểm đến quốc tế, giá cao hơn và dài ngày hơn.
     */
    public function international(): static
    {
        return $this->state(function (array $attributes) {
            $destination = fake()->randomElement(self::FOREIGN_DESTINATIONS);
            $departure = fake()->randomElement(self::DEPARTURES);
            $days = fake()->numberBetween(4, 8);

            return [
                'title' => sprintf('Tour %s - %s %dN%dĐ', $departure, $destination, $days, $days - 1),
                'departure_location' => $departure,
                'duration_days' => $days,
                'price' => fake()->numberBetween(30, 80) * 100000 * $days,
                'excluded_services' => implode("\n", [
                    'Chi phí cá nhân, đồ uống',
                    'Tiền tip cho hướng dẫn viên và tài xế',
                    'Lệ phí làm visa và hộ chiếu',
                    'Phụ thu phòng đơn',
                ]),
            ];
        });
    }

    /**
     * Tour thuộc một danh mục cho trước.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->category_id,
        ]);
    }

    /**
     * Tour đang tạm ngừng bán.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
