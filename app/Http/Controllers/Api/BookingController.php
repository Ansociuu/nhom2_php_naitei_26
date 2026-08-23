<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings with filters & status counts.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->bookings()
            ->with([
                'schedule.tour.images' => fn ($q) => $q->orderBy('display_order'),
                'ticketType',
                'payment',
            ]);

        // Filter by keyword (tour title)
        if ($keyword = $request->input('keyword') ?? $request->input('q')) {
            $query->whereHas('schedule.tour', fn ($q) => $q->where('title', 'like', "%{$keyword}%"));
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by time
        if ($request->input('time') === 'upcoming') {
            $query->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '>=', now()->toDateString())
            );
        } elseif ($request->input('time') === 'past') {
            $query->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '<', now()->toDateString())
            );
        }

        $perPage = (int) $request->input('per_page', 10);
        $bookings = $query->latest('booked_at')->paginate($perPage);

        $statusCounts = $user->bookings()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'status'        => 'success',
            'data'          => BookingResource::collection($bookings)->response()->getData(true),
            'status_counts' => [
                'pending'   => (int) ($statusCounts['pending'] ?? 0),
                'confirmed' => (int) ($statusCounts['confirmed'] ?? 0),
                'completed' => (int) ($statusCounts['completed'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
        ]);
    }

    /**
     * Store a newly created booking for a tour.
     */
    public function store(StoreBookingRequest $request, Tour $tour): JsonResponse
    {
        if ($tour->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tour này hiện không khả dụng để đặt chỗ.',
            ], 404);
        }

        $validated = $request->validated();
        $ticketType = $tour->ticketTypes()->find($validated['ticket_type_id']);

        if (! $ticketType) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Loại vé không hợp lệ cho tour này.',
            ], 422);
        }

        $passengers = $validated['passengers'];

        $booking = DB::transaction(function () use ($tour, $validated, $ticketType, $passengers, $request) {
            $schedule = $tour->schedules()->lockForUpdate()->find($validated['schedule_id']);

            if (! $schedule || $schedule->available_slots < count($passengers)) {
                return null;
            }

            $unitPrice = (float) $ticketType->price;
            $numAdults = 0;
            $numChildren = 0;
            $totalAmount = 0;
            $details = [];

            foreach ($passengers as $index => $passenger) {
                $age = isset($passenger['age']) ? (int) $passenger['age'] : null;
                $isChild = $age !== null && $age < 12;

                $isChild ? $numChildren++ : $numAdults++;
                $price = $isChild ? $unitPrice * 0.5 : $unitPrice;
                $totalAmount += $price;

                $details[] = [
                    'name'      => trim($passenger['full_name']),
                    'age'       => $age ?? 0,
                    'price'     => $price,
                    'phone'     => $passenger['phone'] ?? null,
                    'seat_no'   => $passenger['seat_no'] ?? null,
                    'is_booker' => $index === 0,
                ];
            }

            $booking = Booking::create([
                'user_id'        => $request->user()->user_id,
                'schedule_id'    => $schedule->schedule_id,
                'ticket_type_id' => $ticketType->ticket_type_id,
                'num_adults'     => $numAdults,
                'num_children'   => $numChildren,
                'unit_price'     => $unitPrice,
                'total_amount'   => $totalAmount,
                'note'           => $validated['note'] ?? null,
                'status'         => 'pending',
            ]);

            foreach ($details as $detail) {
                $booking->details()->create($detail);
            }

            $schedule->decrement('available_slots', count($passengers));

            return $booking;
        });

        if (! $booking) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lịch khởi hành này không còn đủ chỗ trống.',
            ], 422);
        }

        $booking->load(['schedule.tour.images', 'ticketType', 'details', 'payment']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đặt chỗ thành công! Vui lòng tiến hành thanh toán.',
            'data'    => new BookingResource($booking),
        ], 201);
    }

    /**
     * Display the specified booking detail.
     */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền truy cập đơn đặt chỗ này.',
            ], 403);
        }

        $booking->load(['schedule.tour.images', 'ticketType', 'details', 'payment']);

        return response()->json([
            'status' => 'success',
            'data'   => new BookingResource($booking),
        ]);
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền hủy đơn đặt chỗ này.',
            ], 403);
        }

        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Đơn đặt chỗ ở trạng thái hiện tại không thể hủy.',
            ], 422);
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $booking->schedule()->increment('available_slots', $booking->num_adults + $booking->num_children);
        });

        $booking->load(['schedule.tour.images', 'ticketType', 'details', 'payment']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã hủy đơn đặt chỗ thành công.',
            'data'    => new BookingResource($booking),
        ]);
    }
}
