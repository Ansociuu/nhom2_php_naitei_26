<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bank_account_id'     => $this->bank_account_id,
            'user_id'             => $this->user_id,
            'bank_name'           => $this->bank_name,
            'account_number'      => $this->account_number,
            'account_holder_name' => $this->account_holder_name,
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
