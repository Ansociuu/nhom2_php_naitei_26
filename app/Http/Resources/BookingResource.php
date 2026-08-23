<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'booking_id'     => $this->booking_id,
            'user_id'        => $this->user_id,
            'schedule_id'    => $this->schedule_id,
            'ticket_type_id' => $this->ticket_type_id,
            'num_adults'     => (int) $this->num_adults,
            'num_children'   => (int) $this->num_children,
            'unit_price'     => (float) $this->unit_price,
            'total_amount'   => (float) $this->total_amount,
            'note'           => $this->note,
            'status'         => $this->status,
            'booked_at'      => $this->booked_at?->toIso8601String(),
            'confirmed_at'   => $this->confirmed_at?->toIso8601String(),
            'cancelled_at'   => $this->cancelled_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),

            'schedule'       => $this->whenLoaded('schedule', function () {
                $tour = $this->schedule->tour ?? null;
                $coverImage = $tour && $tour->relationLoaded('images')
                    ? ($tour->images->firstWhere('is_cover', true) ?? $tour->images->first())
                    : null;

                return [
                    'schedule_id'     => $this->schedule->schedule_id,
                    'departure_date'  => $this->schedule->departure_date->format('Y-m-d'),
                    'available_slots' => $this->schedule->available_slots,
                    'tour'            => $tour ? [
                        'tour_id'            => $tour->tour_id,
                        'title'              => $tour->title,
                        'departure_location' => $tour->departure_location,
                        'duration_days'      => $tour->duration_days,
                        'cover_image'        => $coverImage ? [
                            'image_id'   => $coverImage->image_id,
                            'secure_url' => $coverImage->secure_url,
                        ] : null,
                    ] : null,
                ];
            }),

            'ticket_type'    => $this->whenLoaded('ticketType', function () {
                return [
                    'ticket_type_id' => $this->ticketType->ticket_type_id,
                    'name'           => $this->ticketType->name,
                    'price'          => (float) $this->ticketType->price,
                ];
            }),

            'details'        => $this->whenLoaded('details', function () {
                return $this->details->map(fn ($detail) => [
                    'booking_detail_id' => $detail->booking_detail_id,
                    'name'              => $detail->name,
                    'age'               => $detail->age,
                    'price'             => (float) $detail->price,
                    'phone'             => $detail->phone,
                    'seat_no'           => $detail->seat_no,
                    'is_booker'         => (bool) $detail->is_booker,
                ]);
            }),

            'payment'        => $this->whenLoaded('payment', function () {
                if (! $this->payment) {
                    return null;
                }

                return [
                    'payment_id'     => $this->payment->payment_id,
                    'amount'         => (float) $this->payment->amount,
                    'status'         => $this->payment->status,
                    'gateway'        => $this->payment->gateway,
                    'gateway_txn_id' => $this->payment->gateway_txn_id,
                    'expire_at'      => $this->payment->expire_at?->toIso8601String(),
                    'paid_at'        => $this->payment->paid_at?->toIso8601String(),
                ];
            }),
        ];
    }
}
