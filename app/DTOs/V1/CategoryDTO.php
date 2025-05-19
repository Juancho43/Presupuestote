<?php

namespace App\DTOs\V1;

readonly class CategoryDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
    ) {
    }
}
