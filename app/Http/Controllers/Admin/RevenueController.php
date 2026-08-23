<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : Carbon::now()->startOfMonth();
        $to = $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : Carbon::now()->endOfDay();

        // Stats
        $totalRevenue = Payment::where('status', 'success')->whereBetween('paid_at', [$from, $to])->sum('amount');
        $totalTransactions = Payment::where('status', 'success')->whereBetween('paid_at', [$from, $to])->count();
        
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::whereIn('status', ['confirmed', 'completed'])->count();
        
        $avgBookingValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Revenue by tour (top 10)
        $topTours = Payment::where('payments.status', 'success')
            ->whereBetween('payments.paid_at', [$from, $to])
            ->join('bookings', 'payments.booking_id', '=', 'bookings.booking_id')
            ->join('tour_schedules', 'bookings.schedule_id', '=', 'tour_schedules.schedule_id')
            ->join('tours', 'tour_schedules.tour_id', '=', 'tours.tour_id')
            ->selectRaw('tours.tour_id, tours.title, SUM(payments.amount) as total_revenue, COUNT(DISTINCT bookings.booking_id) as booking_count')
            ->groupBy('tours.tour_id', 'tours.title')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $monthlyTrendRaw = Payment::where('status', 'success')
            ->where('paid_at', '>=', $sixMonthsAgo)
            ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyTrend = [];
        $maxMonthlyRevenue = 0;
        foreach ($monthlyTrendRaw as $item) {
            $monthLabel = str_pad($item->month, 2, '0', STR_PAD_LEFT) . '/' . $item->year;
            $monthlyTrend[] = [
                'month' => $monthLabel,
                'revenue' => $item->revenue
            ];
            if ($item->revenue > $maxMonthlyRevenue) {
                $maxMonthlyRevenue = $item->revenue;
            }
        }

        // Recent transactions
        $recentTransactions = Payment::with(['booking.user', 'booking.schedule.tour'])
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'totalTransactions',
            'totalBookings',
            'confirmedBookings',
            'avgBookingValue',
            'topTours',
            'monthlyTrend',
            'maxMonthlyRevenue',
            'recentTransactions',
            'from',
            'to'
        ));
    }
}
