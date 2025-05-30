<?php

namespace App\DTOs\V1;

readonly class MeasureDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
        public string|null $abbreviation = null,
    ) {
    }
}
