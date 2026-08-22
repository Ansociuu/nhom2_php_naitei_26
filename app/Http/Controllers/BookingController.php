<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()
            ->bookings()
            ->with(['schedule.tour.images', 'ticketType'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->whereHas('schedule.tour', fn ($q) => $q->where('title', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->input('time') === 'upcoming', fn ($q) => $q->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '>=', now()->toDateString())
            ))
            ->when($request->input('time') === 'past', fn ($q) => $q->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '<', now()->toDateString())
            ));

        $bookings = (clone $query)
            ->latest('booked_at')
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', [
            'bookings' => $bookings,
            'statusCounts' => $request->user()->bookings()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function create(Tour $tour): View
    {
        $tour->load([
            'ticketTypes',
            'schedules' => fn ($query) => $query->where('departure_date', '>=', now()->toDateString())->orderBy('departure_date'),
        ]);

        return view('bookings.create', ['tour' => $tour]);
    }

    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:tour_schedules,schedule_id'],
            'ticket_type_id' => ['required', 'exists:ticket_types,ticket_type_id'],
            'note' => ['nullable', 'string'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'passengers.*.phone' => ['nullable', 'string', 'max:30'],
            'passengers.*.seat_no' => ['nullable', 'string', 'max:20'],
        ]);

        $ticketType = $tour->ticketTypes()->findOrFail($validated['ticket_type_id']);
        $passengers = $validated['passengers'];
        $numChildren = collect($passengers)->filter(fn ($p) => isset($p['age']) && $p['age'] < 12)->count();

        $booking = DB::transaction(function () use ($tour, $validated, $ticketType, $passengers, $numChildren, $request) {
            $schedule = $tour->schedules()->lockForUpdate()->findOrFail($validated['schedule_id']);

            if ($schedule->available_slots < count($passengers)) {
                return null;
            }

            $schedule->decrement('available_slots', count($passengers));

            $booking = Booking::create([
                'user_id' => $request->user()->user_id,
                'schedule_id' => $schedule->schedule_id,
                'ticket_type_id' => $ticketType->ticket_type_id,
                'num_adults' => count($passengers) - $numChildren,
                'num_children' => $numChildren,
                'unit_price' => $ticketType->price,
                'total_amount' => $ticketType->price * count($passengers),
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($passengers as $index => $passenger) {
                $booking->passengers()->create([
                    'full_name' => $passenger['full_name'],
                    'age' => $passenger['age'] ?? null,
                    'phone' => $passenger['phone'] ?? null,
                    'seat_no' => $passenger['seat_no'] ?? null,
                    'is_booker' => $index === 0,
                ]);
            }

            return $booking;
        });

        if (! $booking) {
            return back()->withInput()->with('error', 'Chuyến đi này không còn đủ chỗ trống.');
        }

        return redirect()->route('bookings.show', $booking)->with('status', 'Đặt chỗ thành công!');
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load(['schedule.tour', 'ticketType', 'passengers']);

        return view('bookings.show', ['booking' => $booking]);
    }
}
