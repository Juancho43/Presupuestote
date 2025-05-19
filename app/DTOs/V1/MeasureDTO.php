<?php

namespace App\DTOs\V1;

use Ramsey\Uuid\Type\Decimal;

readonly class MeasureDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
        public string|null $description = null,
        public decimal|null $unit = null,
    ) {
    }
}
