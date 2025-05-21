<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'brand' => $this->brand,
            'quantity' => $this->pivot->quantity ?? null,
            'latestPrice' => new PriceResource($this->latestPrice),
            'subcategory' => new SubCategoryResource($this->whenLoaded('subcategory')),
            'prices' => PriceResource::collection($this->whenLoaded('prices')),
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            'measure' => new MeasureResource($this->whenLoaded('measure')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString()
        ];
    }
}
