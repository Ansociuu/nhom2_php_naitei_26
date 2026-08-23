<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
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
            'departure_location' => $this->departure_location,
            'price'              => (float) $this->price,
            'duration_days'      => $this->duration_days,
            'status'             => $this->status,
            'cover_image'        => $coverImage ? [
                'image_id'   => $coverImage->image_id,
                'secure_url' => $coverImage->secure_url,
            ] : null,
            'category'           => new CategoryResource($this->whenLoaded('category')),
            'reviews_avg_score'  => isset($this->reviews_avg_score) ? round((float) $this->reviews_avg_score, 1) : null,
            'reviews_count'      => isset($this->reviews_count) ? (int) $this->reviews_count : null,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
