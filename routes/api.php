<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TourController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Categories API
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Tours API
    Route::get('/tours', [TourController::class, 'index']);
    Route::get('/tours/featured', [TourController::class, 'featured']);
    Route::get('/tours/locations', [TourController::class, 'locations']);
    Route::get('/tours/{tour}', [TourController::class, 'show']);
});
