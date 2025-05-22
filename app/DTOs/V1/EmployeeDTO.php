<?php

namespace App\DTOs\V1;

use Carbon\Carbon;

readonly class EmployeeDTO
{
    public function __construct(
        public int|null $id = null,
        public float|null  $salary = null,
        public Carbon|null  $startDate = null,
        public Carbon|null  $endDate = null,
        public bool|null  $isActive = null,
        public PersonDTO|null  $person = null,
        ) {
    }
}
