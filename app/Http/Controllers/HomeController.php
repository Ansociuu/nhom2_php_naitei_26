<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Trang tổng quan tài khoản: thống kê nhanh + chuyến sắp tới + gợi ý tour.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $upcoming = $user->bookings()
            ->with(['schedule.tour.images', 'ticketType'])
            ->whereHas('schedule', fn ($query) => $query->whereDate('departure_date', '>=', now()->toDateString()))
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->sortBy(fn ($booking) => $booking->schedule->departure_date)
            ->take(3);

        $completedCount = $user->bookings()
            ->whereHas('schedule', fn ($query) => $query->whereDate('departure_date', '<', now()->toDateString()))
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        $suggested = Tour::query()
            ->where('status', 'active')
            ->whereIn('region', ['mien_bac', 'mien_nam'])
            ->whereNotIn('tour_id', $user->bookings()
                ->join('tour_schedules', 'bookings.schedule_id', '=', 'tour_schedules.schedule_id')
                ->pluck('tour_schedules.tour_id'))
            ->with(['images', 'ticketTypes'])
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('dashboard', [
            'upcoming' => $upcoming,
            'stats' => [
                'total' => $user->bookings()->count(),
                'completed' => $completedCount,
                'reviews' => $user->reviews()->count(),
            ],
            'suggested' => $suggested,
        ]);
    }

    public function index(): View
    {
        $northTours = Tour::query()
            ->where('status', 'active')
            ->where('region', 'mien_bac')
            ->with(['images', 'ticketTypes'])
            ->latest('tour_id')
            ->take(6)
            ->get();

        $southTours = Tour::query()
            ->where('status', 'active')
            ->where('region', 'mien_nam')
            ->with(['images', 'ticketTypes'])
            ->latest('tour_id')
            ->take(6)
            ->get();

        return view('home.index', [
            'northTours' => $northTours,
            'southTours' => $southTours,
        ]);
    }
}
