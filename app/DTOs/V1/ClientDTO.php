<?php

namespace App\DTOs\V1;

readonly class ClientDTO
{
    public function __construct(
        public int|null $id = null,
        public float|null  $balance = null,
        public PersonDTO|null  $person = null,
        ) {
    }
}
