<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\Payment;
use App\Models\TicketType;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access payment checkout endpoint', function () {
    $this->getJson('/api/v1/bookings/1/pay')->assertStatus(401);
});

test('user can get QR checkout details for pending booking via API', function () {
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

    $response = $this->actingAs($user)->getJson("/api/v1/bookings/{$booking->booking_id}/pay");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data'   => [
                'booking_id' => $booking->booking_id,
                'amount'     => 1500000,
                'gateway'    => 'qr_mock',
                'status'     => 'pending',
            ],
        ]);

    $this->assertDatabaseHas('payments', [
        'booking_id' => $booking->booking_id,
        'amount'     => 1500000,
        'status'     => 'pending',
    ]);
});

test('user cannot checkout payment for another user booking', function () {
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

    $response = $this->actingAs($otherUser)->getJson("/api/v1/bookings/{$booking->booking_id}/pay");

    $response->assertStatus(403)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Bạn không có quyền truy cập thanh toán cho đơn hàng này.',
        ]);
});

test('user cannot checkout payment for confirmed or cancelled booking', function () {
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
        'status'         => 'confirmed',
    ]);

    $response = $this->actingAs($user)->getJson("/api/v1/bookings/{$booking->booking_id}/pay");

    $response->assertStatus(422)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Đơn đặt chỗ này không ở trạng thái chờ thanh toán.',
        ]);
});

test('public client can poll payment transaction status via API', function () {
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

    $payment = Payment::create([
        'booking_id'     => $booking->booking_id,
        'amount'         => 1500000,
        'status'         => 'pending',
        'gateway'        => 'qr_mock',
        'gateway_txn_id' => 'txn_test12345678',
        'expire_at'      => now()->addMinutes(15),
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->gateway_txn_id}/status");

    $response->assertStatus(200)
        ->assertJson([
            'status'         => 'success',
            'payment_status' => 'pending',
            'booking_id'     => $booking->booking_id,
            'amount'         => 1500000,
        ]);
});

test('polling status returns 404 for invalid transaction ID', function () {
    $response = $this->getJson('/api/v1/payments/txn_invalid_9999/status');

    $response->assertStatus(404)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Giao dịch không tồn tại.',
        ]);
});
