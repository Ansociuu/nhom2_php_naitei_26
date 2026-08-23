<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display the QR checkout page for a pending booking.
     */
    public function checkout(Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === request()->user()->user_id, 403);

        if ($booking->status !== 'pending') {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('status', 'booking-already-processed');
        }

        $booking->load(['schedule.tour', 'details']);

        // Find existing pending payment with remaining time or create a new one
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

        return view('payments.checkout', [
            'booking'   => $booking,
            'payment'   => $payment,
            'scanUrl'   => $scanUrl,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Polling endpoint to check transaction status.
     */
    public function status(string $txn): JsonResponse
    {
        $payment = Payment::where('gateway_txn_id', $txn)->first();

        if (! $payment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Check if expired while pending
        if ($payment->status === 'pending' && $payment->expire_at && $payment->expire_at->isPast()) {
            $payment->update(['status' => 'failed']);
        }

        return response()->json([
            'status'       => $payment->status,
            'booking_id'   => $payment->booking_id,
            'redirect_url' => route('bookings.show', $payment->booking_id),
        ]);
    }

    /**
     * Public scan endpoint hit when phone scans the QR code.
     */
    public function scan(string $txn): Response
    {
        $payment = Payment::where('gateway_txn_id', $txn)->first();

        if (! $payment) {
            return response(
                '<div style="font-family:sans-serif;text-align:center;padding:50px 20px;color:#dc2626;"><h3>Mã giao dịch không hợp lệ.</h3></div>',
                404
            );
        }

        if ($payment->expire_at && $payment->expire_at->isPast() && $payment->status === 'pending') {
            $payment->update(['status' => 'failed']);
            return response(
                '<div style="font-family:sans-serif;text-align:center;padding:50px 20px;color:#dc2626;"><h3>Giao dịch này đã hết hạn.</h3></div>',
                400
            );
        }

        if ($payment->status === 'pending') {
            DB::transaction(function () use ($payment) {
                $payment->update([
                    'status'  => 'success',
                    'paid_at' => now(),
                ]);

                $payment->booking->update([
                    'status'       => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            });
        }

        return response(
            '<!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Thanh toán thành công</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background-color: #f0fdf4; color: #166534; text-align: center; padding: 20px; }
                    .card { background: white; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); max-width: 360px; width: 100%; }
                    .icon { width: 64px; height: 64px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold; margin-bottom: 20px; }
                    h2 { margin: 0 0 10px; font-size: 22px; color: #15803d; }
                    p { margin: 0; color: #6b7280; font-size: 14px; line-height: 1.5; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="icon">✓</div>
                    <h2>Thanh toán thành công!</h2>
                    <p>Giao dịch đã được hệ thống ghi nhận. Màn hình đặt tour của bạn sẽ tự động cập nhật.</p>
                </div>
            </body>
            </html>'
        );
    }
}
