<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TourController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('tours', TourController::class);
});

require __DIR__.'/auth.php';

