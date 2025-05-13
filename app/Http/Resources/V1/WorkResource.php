<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
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
            'order' => $this->order,
            'name' => $this->name,
            'notes' => $this->notes,
            'estimated_time' => $this->estimated_time,
            'dead_line' => $this->dead_line?->toDateString(),
            'cost' => $this->cost,
            'status' => $this->status->value,
            'materials' => MaterialResource::collection($this->whenLoaded('materials')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString()
        ];
    }
}
