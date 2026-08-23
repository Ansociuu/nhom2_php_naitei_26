<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display the user's booking history with optional status filter.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $bookings = Booking::where('user_id', $request->user()->user_id)
            ->with(['schedule.tour', 'payment', 'details'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('booked_at')
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', [
            'bookings'      => $bookings,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Show the booking form for a specific tour.
     */
    public function create(Tour $tour): View
    {
        $schedules = $tour->schedules()
            ->where('departure_date', '>', now())
            ->where('available_slots', '>', 0)
            ->orderBy('departure_date')
            ->get();

        return view('bookings.create', [
            'tour'      => $tour,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Store a new booking with passenger details.
     * Uses a DB transaction to atomically create the booking, booking details, and decrement available slots.
     */
    public function store(BookingStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $booking = DB::transaction(function () use ($validated, $request) {
            $schedule = TourSchedule::with('tour')->lockForUpdate()->find($validated['schedule_id']);

            $passengers = $validated['passengers'];
            $totalGuests = count($passengers);

            // Double-check slots under lock
            if ($totalGuests > $schedule->available_slots) {
                abort(422, 'Không đủ chỗ trống. Vui lòng thử lại.');
            }

            // Calculate pricing and classify passengers
            $unitPrice   = $schedule->price_override ?? $schedule->tour->price;
            $numAdults   = 0;
            $numChildren = 0;
            $totalAmount = 0;
            $detailRecords = [];

            foreach ($passengers as $p) {
                $age = intval($p['age']);
                $isAdult = $age >= 12;

                if ($isAdult) {
                    $numAdults++;
                    $passengerPrice = $unitPrice;
                } else {
                    $numChildren++;
                    $passengerPrice = $unitPrice * 0.5;
                }

                $totalAmount += $passengerPrice;
                $detailRecords[] = [
                    'name'  => trim($p['name']),
                    'age'   => $age,
                    'price' => $passengerPrice,
                ];
            }

            // Create booking record
            $booking = Booking::create([
                'user_id'      => $request->user()->user_id,
                'schedule_id'  => $schedule->schedule_id,
                'num_adults'   => $numAdults,
                'num_children' => $numChildren,
                'unit_price'   => $unitPrice,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
            ]);

            foreach ($detailRecords as $record) {
                $booking->details()->create($record);
            }

            $schedule->decrement('available_slots', $totalGuests);

            return $booking;
        });

        return redirect()
            ->route('bookings.pay', $booking)
            ->with('status', 'booking-created');
    }

    /**
     * Display a single booking (invoice / voucher view).
     */
    public function show(Booking $booking): View
    {
        // Only the owner can view their booking
        abort_unless($booking->user_id === request()->user()->user_id, 403);

        $booking->load(['schedule.tour', 'payment', 'details']);

        return view('bookings.show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Cancel a booking and restore the reserved slots back to the schedule.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === request()->user()->user_id, 403);

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['status' => 'Không thể hủy đơn đặt tour này.']);
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Restore slots to the schedule
            $totalGuests = $booking->num_adults + $booking->num_children;
            $booking->schedule()->increment('available_slots', $totalGuests);
        });

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', 'booking-cancelled');
    }
}
