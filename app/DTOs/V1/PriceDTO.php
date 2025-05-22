<?php

namespace App\DTOs\V1;

use Carbon\Carbon;

readonly class PriceDTO
{
    public function __construct(
        public int|null $id = null,
        public float|null $price = null,
        public Carbon|null $date = null,
        public MaterialDTO|null $material = null,
        ) {
    }
}
