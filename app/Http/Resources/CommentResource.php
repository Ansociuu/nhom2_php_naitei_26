<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'comment_id'        => $this->comment_id,
            'review_id'         => $this->review_id,
            'user_id'           => $this->user_id,
            'parent_comment_id' => $this->parent_comment_id,
            'content'           => $this->content,
            'user'              => $this->whenLoaded('user', function () {
                return [
                    'user_id'  => $this->user->user_id,
                    'username' => $this->user->username,
                ];
            }),
            'replies'           => CommentResource::collection($this->whenLoaded('replies')),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
