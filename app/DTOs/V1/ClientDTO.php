<?php

namespace App\DTOs\V1;

use Ramsey\Uuid\Type\Decimal;

readonly class ClientDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null  $balance = null,
        public PersonDTO|null  $person = null,
        ) {
    }
}
