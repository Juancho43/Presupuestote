<?php

namespace App\DTOs\V1;

use Carbon\Carbon;

readonly class StockDTO
{
    public function __construct(
        public int|null $id = null,
        public float|null $stock = null,
        public Carbon|null $date = null,
        public MaterialDTO|null $material = null,

    ) {
    }
}
