<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalBookings = Booking::count();
        $pendingBookingsCount = Booking::where('status', 'pending')->count();
        $activeToursCount = Tour::where('status', 'active')->count();
        $totalUsersCount = User::count();
        $pendingReviewsCount = Review::where('status', 'pending')->count();

        $latestBookings = Booking::with(['user', 'schedule.tour'])
            ->orderByDesc('booking_id')
            ->limit(6)
            ->get();

        $latestPendingReviews = Review::with(['user', 'tour', 'images'])
            ->where('status', 'pending')
            ->orderByDesc('review_id')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'totalBookings',
            'pendingBookingsCount',
            'activeToursCount',
            'totalUsersCount',
            'pendingReviewsCount',
            'latestBookings',
            'latestPendingReviews'
        ));
    }
}
