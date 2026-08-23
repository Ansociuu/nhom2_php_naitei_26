<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Get or initialize QR checkout details for a pending booking.
     */
    public function checkout(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền truy cập thanh toán cho đơn hàng này.',
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Đơn đặt chỗ này không ở trạng thái chờ thanh toán.',
            ], 422);
        }

        // Find existing pending payment or create new
        $payment = Payment::where('booking_id', $booking->booking_id)
            ->where('status', 'pending')
            ->where('expire_at', '>', now())
            ->first();

        if (! $payment) {
            $payment = Payment::create([
                'booking_id'     => $booking->booking_id,
                'amount'         => $booking->total_amount,
                'status'         => 'pending',
                'gateway'        => 'qr_mock',
                'gateway_txn_id' => 'txn_' . strtolower(Str::random(12)),
                'expire_at'      => now()->addMinutes(15),
            ]);
        }

        $baseUrl = config('services.payment.scan_base_url') ?: url('/');
        $scanUrl = rtrim($baseUrl, '/') . '/pay/' . $payment->gateway_txn_id;
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=' . urlencode($scanUrl);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'payment_id'     => $payment->payment_id,
                'booking_id'     => $booking->booking_id,
                'amount'         => (float) $payment->amount,
                'gateway'        => $payment->gateway,
                'gateway_txn_id' => $payment->gateway_txn_id,
                'status'         => $payment->status,
                'scan_url'       => $scanUrl,
                'qr_code_url'    => $qrCodeUrl,
                'expire_at'      => $payment->expire_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Polling endpoint to check payment transaction status.
     */
    public function status(string $txn): JsonResponse
    {
        $payment = Payment::where('gateway_txn_id', $txn)->first();

        if (! $payment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Giao dịch không tồn tại.',
            ], 404);
        }

        if ($payment->status === 'pending' && $payment->expire_at && $payment->expire_at->isPast()) {
            $payment->update(['status' => 'failed']);
        }

        return response()->json([
            'status'         => 'success',
            'payment_status' => $payment->status,
            'booking_id'     => $payment->booking_id,
            'amount'         => (float) $payment->amount,
            'paid_at'        => $payment->paid_at?->toIso8601String(),
            'expire_at'      => $payment->expire_at?->toIso8601String(),
        ]);
    }
}
