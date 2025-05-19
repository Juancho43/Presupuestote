<?php

namespace App\DTOs\V1;

readonly class PersonDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $name = null,
        public string|null $last_name = null,
        public string|null $address = null,
        public string|null $phone_number = null,
        public string|null $mail = null,
        public string|null $dni = null,
        public string|null $cuit = null,
        ) {
    }
}
