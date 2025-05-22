<?php

namespace App\DTOs\V1;


readonly class SupplierDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $notes = null,
        public float|null $balance = null,
        public PersonDTO|null $person = null,
    ) {
    }
}
