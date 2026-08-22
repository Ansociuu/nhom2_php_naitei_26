<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $tables = [];
    $tableNames = Schema::getTableListing();

    foreach ($tableNames as $tableName) {
        $columns = Schema::getColumnListing($tableName);
        $rows = DB::table($tableName)->limit(100)->get();

        $tables[] = [
            'name' => $tableName,
            'columns' => $columns,
            'rows' => $rows,
            'count' => DB::table($tableName)->count(),
        ];
    }

    return view('welcome', compact('tables'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bank-account', [ProfileController::class, 'updateBankAccount'])->name('profile.bank.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings & Payment Checkout
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/tours/{tour}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'checkout'])->name('bookings.pay');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Real-time Payment Status Polling (API)
Route::get('/payments/{txn}/status', [PaymentController::class, 'status'])->name('payments.status');

// Public Mobile QR Code Scan Endpoint
Route::get('/pay/{txn}', [PaymentController::class, 'scan'])->name('pay.scan');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('tours', TourController::class);
});

require __DIR__.'/auth.php';

