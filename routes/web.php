<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\TourImageController;
use App\Http\Controllers\Admin\TourItineraryController;
use App\Http\Controllers\Admin\TourScheduleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// API Documentation Interactive Viewer (Swagger UI)
Route::get('/api/docs', function () {
    return view('api-docs');
})->name('api.docs');

Route::get('/api/docs/json', function () {
    $path = base_path('docs/user_api_docs.json');
    if (! file_exists($path)) {
        abort(404, 'Tài liệu API JSON không tồn tại.');
    }
    return response()->json(json_decode(file_get_contents($path), true));
})->name('api.docs.json');

Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bank-account', [ProfileController::class, 'updateBankAccount'])->name('profile.bank.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings & Payment Checkout
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/tours/{tour}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/tours/{tour}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'checkout'])->name('bookings.pay');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/bookings/{booking}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

// Payment
Route::get('/payments/{txn}/status', [PaymentController::class, 'status'])->name('payments.status');

// Public Mobile QR Code Scan Endpoint
Route::get('/pay/{txn}', [PaymentController::class, 'scan'])->name('pay.scan');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Categories & Tours
    Route::resource('categories', CategoryController::class);
    Route::resource('tours', AdminTourController::class);

    // Tour Images
    Route::post('tours/{tour}/images', [TourImageController::class, 'store'])->name('tours.images.store');
    Route::patch('tours/{tour}/images/{image}/cover', [TourImageController::class, 'setCover'])->name('tours.images.cover');
    Route::delete('tours/{tour}/images/{image}', [TourImageController::class, 'destroy'])->name('tours.images.destroy');

    // Users
    Route::resource('users', AdminUserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Bookings (Admin)
    Route::resource('bookings', AdminBookingController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/refund', [AdminBookingController::class, 'refund'])->name('bookings.refund');
    Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::post('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Revenue
    Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');

    // Tour Itineraries
    Route::post('tours/{tour}/itineraries', [TourItineraryController::class, 'store'])->name('tours.itineraries.store');
    Route::put('tours/{tour}/itineraries/{itinerary}', [TourItineraryController::class, 'update'])->name('tours.itineraries.update');
    Route::delete('tours/{tour}/itineraries/{itinerary}', [TourItineraryController::class, 'destroy'])->name('tours.itineraries.destroy');

    // Tour Schedules
    Route::post('tours/{tour}/schedules', [TourScheduleController::class, 'store'])->name('tours.schedules.store');
    Route::put('tours/{tour}/schedules/{schedule}', [TourScheduleController::class, 'update'])->name('tours.schedules.update');
    Route::delete('tours/{tour}/schedules/{schedule}', [TourScheduleController::class, 'destroy'])->name('tours.schedules.destroy');
});

/**
 * Quản lý người dùng dùng middleware `admin` (kiểm tra cột users.role) thay vì
 * `role:admin` của Spatie, vì tài khoản admin hiện được phân quyền qua cột này.
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
