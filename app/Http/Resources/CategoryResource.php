<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id'        => $this->category_id,
            'name'               => $this->name,
            'parent_id'          => $this->parent_id,
            'active_tours_count' => $this->when(isset($this->active_tours_count), $this->active_tours_count),
            'parent'             => new CategoryResource($this->whenLoaded('parent')),
            'children'           => CategoryResource::collection($this->whenLoaded('children')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
