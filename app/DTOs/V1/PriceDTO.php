<?php

namespace App\DTOs\V1;

use Carbon\Carbon;
use Ramsey\Uuid\Type\Decimal;

readonly class PriceDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null $price = null,
        public Carbon|null $date = null,
        public MaterialDTO|null $material = null,
        ) {
    }
}
