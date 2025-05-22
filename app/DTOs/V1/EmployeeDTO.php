<?php

namespace App\DTOs\V1;

use Carbon\Carbon;
use Ramsey\Uuid\Type\Decimal;

readonly class EmployeeDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null  $salary = null,
        public Carbon|null  $startDate = null,
        public Carbon|null  $endDate = null,
        public bool|null  $isActive = null,
        public PersonDTO|null  $person = null,
        ) {
    }
}
