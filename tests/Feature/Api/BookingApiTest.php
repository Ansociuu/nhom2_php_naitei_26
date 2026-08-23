<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\TicketType;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access booking endpoints', function () {
    $this->getJson('/api/v1/bookings')->assertStatus(401);
    $this->getJson('/api/v1/bookings/1')->assertStatus(401);
    $this->postJson('/api/v1/bookings/1/cancel')->assertStatus(401);
});

test('user can book tour successfully via API', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'status'      => 'active',
    ]);
    $ticketType = TicketType::create([
        'tour_id' => $tour->tour_id,
        'name'    => 'Vé Tiêu Chuẩn',
        'price'   => 1000000,
    ]);
    $schedule = TourSchedule::factory()->create([
        'tour_id'         => $tour->tour_id,
        'available_slots' => 10,
        'departure_date'  => now()->addDays(5)->toDateString(),
    ]);

    $payload = [
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'note'           => 'Yêu cầu hỗ trợ ăn chay',
        'passengers'     => [
            [
                'full_name' => 'Nguyễn Văn A',
                'age'       => 30,
                'phone'     => '0901234567',
                'seat_no'   => 'A1',
            ],
            [
                'full_name' => 'Nguyễn Văn B (Trẻ em)',
                'age'       => 10, // Under 12 -> 50% price (500k)
                'phone'     => null,
                'seat_no'   => 'A2',
            ],
        ],
    ];

    $response = $this->actingAs($user)->postJson("/api/v1/tours/{$tour->tour_id}/book", $payload);

    $response->assertStatus(201)
        ->assertJson([
            'status'  => 'success',
            'message' => 'Đặt chỗ thành công! Vui lòng tiến hành thanh toán.',
            'data'    => [
                'num_adults'   => 1,
                'num_children' => 1,
                'unit_price'   => 1000000,
                'total_amount' => 1500000, // 1m + 500k
                'status'       => 'pending',
            ],
        ]);

    $this->assertDatabaseHas('bookings', [
        'user_id'      => $user->user_id,
        'schedule_id'  => $schedule->schedule_id,
        'num_adults'   => 1,
        'num_children' => 1,
        'total_amount' => 1500000,
        'status'       => 'pending',
    ]);

    // Check slots decremented from 10 to 8
    $this->assertDatabaseHas('tour_schedules', [
        'schedule_id'     => $schedule->schedule_id,
        'available_slots' => 8,
    ]);
});

test('cannot book tour when available slots are insufficient', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id, 'status' => 'active']);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Tiêu Chuẩn', 'price' => 1000000]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id, 'available_slots' => 1]);

    $payload = [
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'passengers'     => [
            ['full_name' => 'Khách 1', 'age' => 25],
            ['full_name' => 'Khách 2', 'age' => 26],
        ],
    ];

    $response = $this->actingAs($user)->postJson("/api/v1/tours/{$tour->tour_id}/book", $payload);

    $response->assertStatus(422)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Lịch khởi hành này không còn đủ chỗ trống.',
        ]);
});

test('user can view list of their bookings via API', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id, 'title' => 'Tour Phú Quốc']);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé VIP', 'price' => 2000000]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id]);

    Booking::create([
        'user_id'        => $user->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 1,
        'num_children'   => 0,
        'unit_price'     => 2000000,
        'total_amount'   => 2000000,
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/bookings');

    $response->assertStatus(200)
        ->assertJson([
            'status'        => 'success',
            'status_counts' => [
                'pending' => 1,
            ],
        ])
        ->assertJsonCount(1, 'data.data');
});

test('user can view specific booking detail via API', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Standard', 'price' => 1500000]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id]);

    $booking = Booking::create([
        'user_id'        => $user->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 1,
        'num_children'   => 0,
        'unit_price'     => 1500000,
        'total_amount'   => 1500000,
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($user)->getJson("/api/v1/bookings/{$booking->booking_id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data'   => [
                'booking_id' => $booking->booking_id,
                'status'     => 'pending',
            ],
        ]);
});

test('user cannot view another user booking', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Standard', 'price' => 1500000]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id]);

    $booking = Booking::create([
        'user_id'        => $owner->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 1,
        'num_children'   => 0,
        'unit_price'     => 1500000,
        'total_amount'   => 1500000,
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($otherUser)->getJson("/api/v1/bookings/{$booking->booking_id}");

    $response->assertStatus(403)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Bạn không có quyền truy cập đơn đặt chỗ này.',
        ]);
});

test('user can cancel pending booking via API and restore slots', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $ticketType = TicketType::create(['tour_id' => $tour->tour_id, 'name' => 'Vé Standard', 'price' => 1500000]);
    $schedule = TourSchedule::factory()->create([
        'tour_id'         => $tour->tour_id,
        'available_slots' => 5,
    ]);

    $booking = Booking::create([
        'user_id'        => $user->user_id,
        'schedule_id'    => $schedule->schedule_id,
        'ticket_type_id' => $ticketType->ticket_type_id,
        'num_adults'     => 2,
        'num_children'   => 1,
        'unit_price'     => 1500000,
        'total_amount'   => 3750000,
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/bookings/{$booking->booking_id}/cancel");

    $response->assertStatus(200)
        ->assertJson([
            'status'  => 'success',
            'message' => 'Đã hủy đơn đặt chỗ thành công.',
            'data'    => [
                'booking_id' => $booking->booking_id,
                'status'     => 'cancelled',
            ],
        ]);

    $this->assertDatabaseHas('bookings', [
        'booking_id' => $booking->booking_id,
        'status'     => 'cancelled',
    ]);

    // Check available slots restored (5 + 3 = 8)
    $this->assertDatabaseHas('tour_schedules', [
        'schedule_id'     => $schedule->schedule_id,
        'available_slots' => 8,
    ]);
});
