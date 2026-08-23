<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Review;
use App\Models\TicketType;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('unauthenticated user cannot create review or comment', function () {
    $this->getJson('/api/v1/reviews/my-reviews')->assertStatus(401);
    $this->postJson('/api/v1/bookings/1/review')->assertStatus(401);
    $this->postJson('/api/v1/reviews/1/comments')->assertStatus(401);
    $this->postJson('/api/v1/reviews/1/like')->assertStatus(401);
});

test('user cannot review booking if tour has not ended or booking not completed', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Standard', 'price' => 1000000]);
    
    // Future departure date
    $schedule = TourSchedule::factory()->create([
        'tour_id'        => $tour->tour_id,
        'departure_date' => now()->addDays(5)->toDateString(),
    ]);

    $booking = Booking::create([
        'user_id'        => $user->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 1,
        'num_children'   => 0,
        'unit_price'     => 1000000,
        'total_amount'   => 1000000,
        'status'         => 'confirmed',
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/bookings/{$booking->booking_id}/review", [
        'score'   => 5,
        'content' => 'Tour tuyệt vời ngoài mong đợi!',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Chuyến đi chưa kết thúc nên chưa thể đánh giá.',
        ]);
});

test('user can submit review with images for completed tour via API', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Standard', 'price' => 1000000]);
    
    // Past departure date
    $schedule = TourSchedule::factory()->create([
        'tour_id'        => $tour->tour_id,
        'departure_date' => now()->subDays(5)->toDateString(),
    ]);

    $booking = Booking::create([
        'user_id'        => $user->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 1,
        'num_children'   => 0,
        'unit_price'     => 1000000,
        'total_amount'   => 1000000,
        'status'         => 'confirmed',
    ]);

    $image1 = UploadedFile::fake()->create('review1.jpg', 100, 'image/jpeg');
    $image2 = UploadedFile::fake()->create('review2.png', 100, 'image/png');

    $response = $this->actingAs($user)->postJson("/api/v1/bookings/{$booking->booking_id}/review", [
        'score'   => 5,
        'content' => 'Chuyến đi rất vui và hướng dẫn viên nhiệt tình.',
        'images'  => [$image1, $image2],
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status'  => 'success',
            'message' => 'Cảm ơn bạn đã đánh giá! Bài viết sẽ hiển thị sau khi được duyệt.',
            'data'    => [
                'score'   => 5,
                'content' => 'Chuyến đi rất vui và hướng dẫn viên nhiệt tình.',
                'status'  => 'pending',
            ],
        ]);

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->user_id,
        'tour_id' => $tour->tour_id,
        'score'   => 5,
        'status'  => 'pending',
    ]);

    $this->assertDatabaseCount('review_images', 2);
});

test('user can view list of my reviews and pending review bookings via API', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    
    Review::create([
        'user_id' => $user->user_id,
        'tour_id' => $tour->tour_id,
        'score'   => 5,
        'content' => 'Đánh giá đã gửi trước đó',
        'status'  => 'pending',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/reviews/my-reviews');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonCount(1, 'my_reviews');
});

test('can list approved tour reviews publicly via API', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $reviewer = User::factory()->create();

    Review::create([
        'user_id'     => $reviewer->user_id,
        'tour_id'     => $tour->tour_id,
        'score'       => 5,
        'content'     => 'Bài đánh giá công khai xuất sắc',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    $response = $this->getJson("/api/v1/tours/{$tour->tour_id}/reviews");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.content', 'Bài đánh giá công khai xuất sắc');
});


test('user can toggle like and unlike on approved review via API', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $reviewer = User::factory()->create();
    $user = User::factory()->create();

    $review = Review::create([
        'user_id'     => $reviewer->user_id,
        'tour_id'     => $tour->tour_id,
        'score'       => 5,
        'content'     => 'Bài đánh giá tuyệt vời',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    // Like
    $likeResponse = $this->actingAs($user)->postJson("/api/v1/reviews/{$review->review_id}/like");
    $likeResponse->assertStatus(200)
        ->assertJson([
            'status'      => 'success',
            'is_liked'    => true,
            'likes_count' => 1,
        ]);

    $this->assertDatabaseHas('review_likes', [
        'review_id' => $review->review_id,
        'user_id'   => $user->user_id,
    ]);

    // Toggle Unlike
    $unlikeResponse = $this->actingAs($user)->postJson("/api/v1/reviews/{$review->review_id}/like");
    $unlikeResponse->assertStatus(200)
        ->assertJson([
            'status'      => 'success',
            'is_liked'    => false,
            'likes_count' => 0,
        ]);

    $this->assertDatabaseMissing('review_likes', [
        'review_id' => $review->review_id,
        'user_id'   => $user->user_id,
    ]);
});
