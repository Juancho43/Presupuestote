<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'amount' => (float) $this->amount,
            'date' => (string) $this->date?->toDateTimeString(),
            'description' => (string) $this->description,
            'payable_type' => (string) $this->payable_type,
            'payable_id' => (int) $this->payable_id,
            'payable' => $this->whenLoaded('payable'),
            'created_at' => (string) $this->created_at?->toDateTimeString(),
            'updated_at' => (string) $this->updated_at?->toDateTimeString(),
            'deleted_at' => (string) $this->deleted_at?->toDateTimeString()
        ];
    }
}
