<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum') ?? $request->user();
        $isLikedByMe = false;

        if ($user && $this->relationLoaded('likes')) {
            $isLikedByMe = $this->likes->contains('user_id', $user->user_id);
        }

        return [
            'review_id'     => $this->review_id,
            'user_id'       => $this->user_id,
            'tour_id'       => $this->tour_id,
            'score'         => (int) $this->score,
            'content'       => $this->content,
            'status'        => $this->status,
            'user'          => $this->whenLoaded('user', function () {
                return [
                    'user_id'  => $this->user->user_id,
                    'username' => $this->user->username,
                ];
            }),
            'tour'          => $this->whenLoaded('tour', function () {
                return [
                    'tour_id' => $this->tour->tour_id,
                    'title'   => $this->tour->title,
                ];
            }),
            'images'        => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => [
                    'image_id'      => $img->image_id,
                    'url'           => $img->url(),
                    'display_order' => $img->display_order,
                ]);
            }),
            'likes_count'   => isset($this->likes_count) ? (int) $this->likes_count : ($this->relationLoaded('likes') ? $this->likes->count() : null),
            'is_liked_by_me'=> $this->when($user !== null, $isLikedByMe),
            'comments'      => CommentResource::collection($this->whenLoaded('comments')),
            'approved_at'   => $this->approved_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
