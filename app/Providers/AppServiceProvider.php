<?php

namespace App\Providers;

use App\Models\Tour;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mega menu ở header cần danh sách trail theo miền trên mọi trang.
        View::composer('layouts.site', function ($view) {
            $tours = Tour::query()
                ->where('status', 'active')
                ->whereIn('region', ['mien_bac', 'mien_nam'])
                ->with('images')
                ->orderBy('title')
                ->get();

            $view->with('navToursByRegion', $tours->groupBy('region'));
        });
    }
}
