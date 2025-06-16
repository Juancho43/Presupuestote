<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'total' => $this->total,
            'payment_status' => $this->payment_status,
            'materials' => MaterialResource::collection($this->whenLoaded('materials')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'owner' => new SupplierResource($this->whenLoaded('supplier')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString()
        ];
    }
}
