<?php

namespace App\DTOs\V1;

use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class EmployeeDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null  $salary = null,
        public Date|null  $startDate = null,
        public Date|null  $endDate = null,
        public bool|null  $isActive = null,
        public PersonDTO|null  $person = null,
        ) {
    }
}
