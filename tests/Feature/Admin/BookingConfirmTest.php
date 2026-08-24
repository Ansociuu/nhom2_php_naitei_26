<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('admin can confirm a pending booking manually', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id]);

    $booking = Booking::create([
        'user_id'      => $user->user_id,
        'schedule_id'  => $schedule->schedule_id,
        'num_adults'   => 2,
        'num_children' => 0,
        'unit_price'   => 1000000,
        'total_amount' => 2000000,
        'status'       => 'pending',
        'booked_at'    => now(),
    ]);

    $payment = Payment::create([
        'booking_id'     => $booking->booking_id,
        'amount'         => 2000000,
        'gateway'        => 'vietqr',
        'gateway_txn_id' => 'TXN-TEST-123',
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->booking_id}/confirm");

    $response->assertRedirect();

    $this->assertDatabaseHas('bookings', [
        'booking_id' => $booking->booking_id,
        'status'     => 'confirmed',
    ]);

    $this->assertDatabaseHas('payments', [
        'payment_id' => $payment->payment_id,
        'status'     => 'success',
    ]);
});
