<?php

    namespace App\Http\Resources\V1;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class BudgetResource extends JsonResource
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
                'made_date' => $this->made_date?->toDateString(),
                'description' => $this->description,
                'dead_line' => $this->dead_line?->toDateString(),
                'state' => $this->state,
                'payment_status' => $this->payment_status,
                'cost' => $this->cost,
                'profit' => $this->profit,
                'total' => $this->price,
                'owner' => new ClientResource($this->whenLoaded('client')),
                'payments' => PaymentResource::collection($this->whenLoaded('payments')),
                'works' => WorkResource::collection($this->whenLoaded('works')),
                'created_at' => $this->created_at?->toDateTimeString(),
                'updated_at' => $this->updated_at?->toDateTimeString(),
                'deleted_at' => $this->deleted_at?->toDateTimeString()
            ];
        }
    }
