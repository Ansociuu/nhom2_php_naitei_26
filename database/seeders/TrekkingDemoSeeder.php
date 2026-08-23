<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\TicketType;
use App\Models\TicketTypeHighlight;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourSchedule;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu cho 7 trail (Sun* Booking Tour) dựng lại theo thiết kế Figma
 * (grass-adapt-34065180.figma.site). Vài chỉ số (khoảng cách, độ cao, giá vé)
 * không có trong thiết kế gốc được ước tính hợp lý để demo đủ giao diện.
 */
class TrekkingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = Category::where('name', 'Tour khám phá')->value('category_id')
            ?? Category::query()->value('category_id');

        $tours = [
            [
                'title' => 'Chứa Chan',
                'region' => 'mien_nam',
                'province' => 'Đồng Nai',
                'difficulty' => 2,
                'peak_elevation' => 837,
                'elevation_gain' => 657,
                'distance_km' => 7,
                'duration_label' => 'Khoảng 6 tiếng tùy thể lực',
                'description' => 'Núi Chứa Chan – điểm đến lý tưởng cho những ai yêu thích thiên nhiên và muốn tìm chút bình yên giữa cuộc sống bộn bề. Cung đường trekking nhẹ nhàng dẫn lối bạn qua những triền núi phủ đầy cây xanh, lắng nghe tiếng chim hót và cảm nhận gió núi mát lành. Từ trên đỉnh, bạn sẽ được phóng tầm mắt ngắm nhìn vẻ đẹp bình dị của Xuân Lộc trải dài bất tận. Hành trình còn đưa bạn ghé thăm chùa Bửu Quang tĩnh lặng và cây Đa ba gốc một ngọn huyền bí.',
                'cover' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&h=600&fit=crop&auto=format',
                'base_price' => 419000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600&fit=crop&auto=format', 'title' => 'Người Dẫn Đường', 'description' => 'Đồng hành suốt hành trình, giúp bạn khám phá những góc đẹp nhất và đảm bảo chuyến đi an toàn, trọn vẹn.'],
                    ['image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&fit=crop&auto=format', 'title' => 'Chinh Phục Đỉnh 837M', 'description' => 'Đứng trên đỉnh Núi Chứa Chan, thu trọn vào tầm mắt cảnh sắc Xuân Lộc thanh bình, nơi giao thoa giữa trời và đất Đông Nam Bộ.'],
                    ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&fit=crop&auto=format', 'title' => 'Cung Leo Núi Thách Thức', 'description' => 'Độ dốc cao, vách đá cheo leo và thời tiết khắc nghiệt là thử thách giúp các trailer rèn kỹ thuật và vượt qua giới hạn bản thân.'],
                ],
            ],
            [
                'title' => 'Cát Tiên',
                'region' => 'mien_nam',
                'province' => 'Đồng Nai',
                'difficulty' => 2,
                'peak_elevation' => 450,
                'elevation_gain' => 380,
                'distance_km' => 10,
                'duration_label' => 'Khoảng 5 tiếng tùy thể lực',
                'description' => 'Vườn quốc gia Cát Tiên – khu rừng nguyên sinh quý giá còn sót lại ở Nam Bộ. Những cung đường xuyên rừng già, nghe tiếng chim hót rộn ràng và hơi thở của thiên nhiên hoang sơ.',
                'cover' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=900&h=600&fit=crop&auto=format',
                'base_price' => 389000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&fit=crop&auto=format', 'title' => 'Rừng Nguyên Sinh', 'description' => 'Hàng ngàn loài động thực vật quý hiếm sống trong khu rừng nguyên sinh chưa bị tác động.'],
                    ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&fit=crop&auto=format', 'title' => 'Quan Sát Chim Thú', 'description' => 'Cơ hội quan sát các loài chim quý hiếm và thú rừng trong môi trường tự nhiên.'],
                ],
            ],
            [
                'title' => 'Núi Dinh',
                'region' => 'mien_nam',
                'province' => 'Bà Rịa – Vũng Tàu',
                'difficulty' => 2,
                'peak_elevation' => 504,
                'elevation_gain' => 420,
                'distance_km' => 8,
                'duration_label' => 'Khoảng 4 tiếng tùy thể lực',
                'description' => 'Núi Dinh tọa lạc ngay cạnh biển Vũng Tàu, mang đến trải nghiệm leo núi độc đáo với tầm nhìn ra Biển Đông bao la. Cảnh sắc hùng vĩ từ đỉnh núi nhìn xuống thành phố Bà Rịa và vịnh Gành Rái.',
                'cover' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&h=600&fit=crop&auto=format',
                'base_price' => 399000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&fit=crop&auto=format', 'title' => 'View Biển Đông', 'description' => 'Tầm nhìn 360° từ đỉnh núi nhìn ra Biển Đông và vịnh Gành Rái tuyệt đẹp.'],
                ],
            ],
            [
                'title' => 'Ngũ Long',
                'region' => 'mien_nam',
                'province' => 'Bình Dương',
                'difficulty' => 2,
                'peak_elevation' => 420,
                'elevation_gain' => 350,
                'distance_km' => 9,
                'duration_label' => 'Khoảng 6 tiếng tùy thể lực',
                'description' => 'Ngũ Long Sơn – dãy 5 ngọn núi liên kết nhau tạo thành cung đường trekking thú vị, lý tưởng cho người mới bắt đầu khám phá thiên nhiên miền Nam.',
                'cover' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=900&h=600&fit=crop&auto=format',
                'base_price' => 359000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&fit=crop&auto=format', 'title' => 'Dãy 5 Đỉnh', 'description' => 'Chinh phục lần lượt 5 ngọn núi liên kết, mỗi đỉnh mang một vẻ đẹp riêng.'],
                ],
            ],
            [
                'title' => 'Đá Vách',
                'region' => 'mien_nam',
                'province' => 'Ninh Thuận',
                'difficulty' => 5,
                'peak_elevation' => 620,
                'elevation_gain' => 540,
                'distance_km' => 12,
                'duration_label' => 'Khoảng 8 tiếng tùy thể lực',
                'description' => 'Đá Vách – thử thách đỉnh cao dành cho những trekker kinh nghiệm. Những vách đá thẳng đứng, cung đường hiểm trở và khung cảnh hoang sơ đến nghẹt thở. Từ đỉnh Đá Vách, thu trọn khung cảnh sa mạc cát và biển xanh Ninh Thuận.',
                'cover' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=900&h=600&fit=crop&auto=format',
                'base_price' => 489000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&fit=crop&auto=format', 'title' => 'Vách Đá Dựng Đứng', 'description' => 'Những bức tường đá khổng lồ – thử thách không thể bỏ qua cho mọi trekker.'],
                ],
            ],
            [
                'title' => 'Cúc Phương',
                'region' => 'mien_bac',
                'province' => 'Ninh Bình',
                'difficulty' => 2,
                'peak_elevation' => 300,
                'elevation_gain' => 250,
                'distance_km' => 9,
                'duration_label' => 'Khoảng 5 tiếng tùy thể lực',
                'description' => 'Vườn quốc gia Cúc Phương – khu rừng nguyên sinh đầu tiên của Việt Nam, nơi lưu giữ hàng nghìn loài động thực vật quý hiếm. Những hang động bí ẩn và cây cổ thụ hàng nghìn tuổi.',
                'cover' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=900&h=600&fit=crop&auto=format',
                'base_price' => 379000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&fit=crop&auto=format', 'title' => 'Rừng Nguyên Sinh Đầu Tiên VN', 'description' => 'Nơi sinh sống của hàng nghìn loài động thực vật quý hiếm, gồm nhiều loài đặc hữu Việt Nam.'],
                    ['image' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&fit=crop&auto=format', 'title' => 'Hang Động Bí Ẩn', 'description' => 'Khám phá những hang động triệu năm tuổi sâu trong lòng núi đá vôi Ninh Bình.'],
                ],
            ],
            [
                'title' => 'Đà Bắc',
                'region' => 'mien_bac',
                'province' => 'Hòa Bình',
                'difficulty' => 3,
                'peak_elevation' => 500,
                'elevation_gain' => 430,
                'distance_km' => 10,
                'duration_label' => 'Khoảng 6 tiếng tùy thể lực',
                'description' => 'Đà Bắc – vùng đất huyền bí với hồ Hòa Bình bao la và những ngọn núi hùng vĩ. Cung đường trekking qua bản làng dân tộc, khám phá văn hóa và thiên nhiên Tây Bắc.',
                'cover' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&h=600&fit=crop&auto=format',
                'base_price' => 429000,
                'highlights' => [
                    ['image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&fit=crop&auto=format', 'title' => 'Hồ Hòa Bình', 'description' => 'Ngắm hoàng hôn trên hồ Hòa Bình rộng lớn, khung cảnh thơ mộng như tranh vẽ.'],
                    ['image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600&fit=crop&auto=format', 'title' => 'Văn Hóa Bản Làng', 'description' => 'Ghé thăm bản làng dân tộc Tày, Mường, tìm hiểu văn hóa và ẩm thực địa phương.'],
                ],
            ],
        ];

        foreach ($tours as $data) {
            $tour = Tour::create([
                'category_id' => $categoryId,
                'title' => $data['title'],
                'region' => $data['region'],
                'province' => $data['province'],
                'difficulty' => $data['difficulty'],
                'peak_elevation' => $data['peak_elevation'],
                'elevation_gain' => $data['elevation_gain'],
                'distance_km' => $data['distance_km'],
                'duration_label' => $data['duration_label'],
                'description' => $data['description'],
                'departure_location' => 'TP. Hồ Chí Minh',
                'price' => $data['base_price'],
                'duration_days' => 1,
                'status' => 'active',
            ]);

            TourImage::create([
                'tour_id' => $tour->tour_id,
                'image_url' => $data['cover'],
                'is_cover' => true,
                'display_order' => 0,
            ]);

            TourSchedule::create([
                'tour_id' => $tour->tour_id,
                'departure_date' => now()->addDays(7)->toDateString(),
                'available_slots' => 20,
            ]);
            TourSchedule::create([
                'tour_id' => $tour->tour_id,
                'departure_date' => now()->addDays(14)->toDateString(),
                'available_slots' => 20,
            ]);

            $base = $data['base_price'];

            $lenDuong = TicketType::create([
                'tour_id' => $tour->tour_id,
                'name' => 'Lên Đường',
                'price' => round($base * 0.635, -3),
                'original_price' => round($base * 0.635 * 1.05, -3),
                'target_audience' => 'Trekker có kinh nghiệm, thích tự do khám phá mà không cần Leader - Người dẫn đường.',
                'features' => ['Vé xe', 'Hiking', 'Trong ngày', 'Tracklog'],
                'included_services' => "Vé xe khứ hồi\nNước suối trên xe",
                'excluded_services' => "Leader – Người dẫn đường\nĂn uống cá nhân tại trạm nghỉ",
                'includes_bus' => true,
                'is_recommended' => false,
            ]);

            $khamPha = TicketType::create([
                'tour_id' => $tour->tour_id,
                'name' => 'Khám Phá',
                'price' => $base,
                'original_price' => round($base * 1.075, -3),
                'target_audience' => 'Người mới bắt đầu hoặc thích đi theo nhóm có Leader - Người dẫn đường.',
                'features' => ['Vé xe', 'Hiking', 'Tracklog', 'Leader'],
                'included_services' => "Vé xe khứ hồi\nNước suối trên xe\nLeader – Người dẫn đường\nDịch vụ tắm và vệ sinh",
                'excluded_services' => "Huy chương chinh phục\nĂn uống cá nhân tại trạm nghỉ\nChi phí phát sinh ngoài chương trình",
                'includes_bus' => true,
                'is_recommended' => true,
            ]);

            TicketType::create([
                'tour_id' => $tour->tour_id,
                'name' => 'Nhập Hội',
                'price' => round($base * 0.445, -3),
                'original_price' => round($base * 0.445 * 1.075, -3),
                'target_audience' => 'Dành cho những ai muốn chủ động hơn trong việc di chuyển, tự đến điểm hẹn, gặp gỡ và bắt đầu hành trình cùng những người bạn đồng hành.',
                'features' => ['Hiking', 'Tracklog', 'Leader'],
                'included_services' => "Leader – Người dẫn đường\nTracklog hành trình",
                'excluded_services' => "Vé xe khứ hồi\nĂn uống cá nhân tại trạm nghỉ",
                'includes_bus' => false,
                'is_recommended' => false,
            ]);

            foreach ($data['highlights'] as $order => $highlight) {
                TicketTypeHighlight::create([
                    'ticket_type_id' => $khamPha->ticket_type_id,
                    'image_url' => $highlight['image'],
                    'title' => $highlight['title'],
                    'description' => $highlight['description'],
                    'display_order' => $order,
                ]);
            }

            unset($lenDuong);
        }
    }
}
