<?php

namespace App\DTOs\V1;

use Ramsey\Uuid\Type\Decimal;

readonly class SupplierDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $notes = null,
        public decimal|null $balance = null,
        public PersonDTO|null $person = null,
    ) {
    }
}
