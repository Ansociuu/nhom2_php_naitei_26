<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'schedule.tour', 'payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('booking_id', $search);
                }
                $q->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('booking_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'schedule.tour', 'payment', 'details']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['user', 'schedule.tour', 'details']);

        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'passengers' => 'required|array|min:1',
            'passengers.*.booking_detail_id' => 'required|exists:booking_details,booking_detail_id',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.age' => 'required|integer|min:0|max:120',
        ]);

        DB::transaction(function () use ($request, $booking) {
            $numAdults = 0;
            $numChildren = 0;
            $totalAmount = 0;
            $unitPrice = $booking->unit_price;

            foreach ($request->passengers as $pData) {
                $detail = $booking->details()->where('booking_detail_id', $pData['booking_detail_id'])->first();
                if ($detail) {
                    $age = (int) $pData['age'];
                    $price = ($age >= 12) ? $unitPrice : ($unitPrice * 0.5);

                    $detail->update([
                        'name' => $pData['name'],
                        'age' => $age,
                        'price' => $price,
                    ]);

                    if ($age >= 12) {
                        $numAdults++;
                    } else {
                        $numChildren++;
                    }
                    $totalAmount += $price;
                }
            }

            $booking->update([
                'num_adults' => $numAdults,
                'num_children' => $numChildren,
                'total_amount' => $totalAmount,
            ]);
        });

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Cập nhật thông tin hành khách thành công.');
    }

    public function refund(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            if ($booking->payment) {
                $booking->payment->update([
                    'status' => 'refunded',
                ]);
            }

            if ($booking->status !== 'cancelled') {
                $booking->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                $booking->schedule()->increment('available_slots', $booking->num_adults + $booking->num_children);
            }
        });

        return back()->with('success', 'Đã chuyển trạng thái sang Hoàn tiền (Refunded) và hủy đơn thành công.');
    }

    public function complete(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Chỉ có thể hoàn thành đơn ở trạng thái đã xác nhận.');
        }

        $booking->update([
            'status' => 'completed',
        ]);

        return back()->with('success', 'Đã đánh dấu hoàn thành đơn đặt tour.');
    }

    public function cancel(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Không thể hủy đơn đặt tour ở trạng thái này.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $booking->schedule()->increment('available_slots', $booking->num_adults + $booking->num_children);
        });

        return back()->with('success', 'Đã hủy đơn đặt tour thành công.');
    }
}
