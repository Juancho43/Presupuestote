<?php

namespace App\DTOs\V1;

use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class PriceDTO
{
    public function __construct(
        public int|null $id,
        public decimal|null $price,
        public Date|null $date,
        public MaterialDTO|null $material,
        ) {
    }
}
