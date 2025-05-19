<?php

namespace App\DTOs\V1;

use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class StockDTO
{
    public function __construct(
        public int|null $id,
        public decimal|null $stock,
        public Date|null $date,
        public MaterialDTO|null $material,

    ) {
    }
}
