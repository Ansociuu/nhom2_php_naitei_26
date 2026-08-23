<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $coverImage = $this->images->firstWhere('is_cover', true) ?? $this->images->first();

        return [
            'tour_id'            => $this->tour_id,
            'category_id'        => $this->category_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'highlights'         => $this->highlights,
            'departure_location' => $this->departure_location,
            'price'              => (float) $this->price,
            'duration_days'      => $this->duration_days,
            'included_services'  => $this->included_services,
            'excluded_services'  => $this->excluded_services,
            'status'             => $this->status,
            'category'           => new CategoryResource($this->whenLoaded('category')),
            'cover_image'        => $coverImage ? [
                'image_id'   => $coverImage->image_id,
                'secure_url' => $coverImage->secure_url,
            ] : null,
            'images'             => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => [
                    'image_id'      => $img->image_id,
                    'secure_url'    => $img->secure_url,
                    'is_cover'      => (bool) $img->is_cover,
                    'display_order' => $img->display_order,
                ]);
            }),
            'itineraries'        => $this->whenLoaded('itineraries', function () {
                return $this->itineraries->sortBy('day_number')->values()->map(fn ($item) => [
                    'itinerary_id' => $item->itinerary_id,
                    'day_number'   => $item->day_number,
                    'title'        => $item->title,
                    'description'  => $item->description,
                ]);
            }),
            'schedules'          => $this->whenLoaded('schedules', function () {
                return $this->schedules->map(fn ($sch) => [
                    'schedule_id'     => $sch->schedule_id,
                    'departure_date'  => $sch->departure_date->format('Y-m-d'),
                    'available_slots' => $sch->available_slots,
                    'price_override'  => $sch->price_override ? (float) $sch->price_override : null,
                    'final_price'     => (float) ($sch->price_override ?? $this->price),
                ]);
            }),
            'reviews_avg_score'  => isset($this->reviews_avg_score) ? round((float) $this->reviews_avg_score, 1) : null,
            'reviews_count'      => isset($this->reviews_count) ? (int) $this->reviews_count : null,
            'reviews'            => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(fn ($rev) => [
                    'review_id'   => $rev->review_id,
                    'user_name'   => $rev->user?->username ?? 'Khách hàng',
                    'score'       => $rev->score,
                    'content'     => $rev->content,
                    'approved_at' => $rev->approved_at?->toIso8601String(),
                ]);
            }),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
