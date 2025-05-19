<?php

namespace App\DTOs\V1;

readonly class SubCategoryDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
        public CategoryDTO|null $category = null,
    ) {
    }
}
