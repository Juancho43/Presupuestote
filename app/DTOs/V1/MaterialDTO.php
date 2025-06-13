<?php

namespace App\DTOs\V1;

readonly class MaterialDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
        public string|null $description = null,
        public string|null $color = null,
        public string|null $brand = null,
        public float|null $unit_measure = null,
        public SubCategoryDTO|null $subcategory = null,
        public MeasureDTO|null $measure = null,
        public PriceDTO|null $price = null,
        public StockDTO|null $stock = null,
        ) {
    }
}
