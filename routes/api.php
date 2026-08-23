<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TourController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth API
    Route::prefix('auth')->group(function () {
        // Public Auth Routes
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Protected Auth Routes (Require Bearer Token)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'updatePassword']);
            Route::get('/bank-account', [AuthController::class, 'getBankAccount']);
            Route::put('/bank-account', [AuthController::class, 'updateBankAccount']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Categories API
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Tours API
    Route::get('/tours', [TourController::class, 'index']);
    Route::get('/tours/featured', [TourController::class, 'featured']);
    Route::get('/tours/locations', [TourController::class, 'locations']);
    Route::get('/tours/{tour}', [TourController::class, 'show']);

    // Public Payment Status Polling API
    Route::get('/payments/{txn}/status', [PaymentController::class, 'status']);

    // Protected User Routes (Require Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        // Booking API
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/tours/{tour}/book', [BookingController::class, 'store']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

        // Payment Checkout API
        Route::get('/bookings/{booking}/pay', [PaymentController::class, 'checkout']);
    });
});
