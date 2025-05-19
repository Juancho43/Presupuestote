<?php

namespace App\DTOs\V1;

use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class StockDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null $stock = null,
        public Date|null $date = null,
        public MaterialDTO|null $material = null,

    ) {
    }
}
