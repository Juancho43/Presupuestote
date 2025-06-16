<?php

namespace App\DTOs\V1;

use App\States\PaymentState\PaymentState;
use Carbon\Carbon;

readonly class SalaryDTO
{
    public function __construct(
        public int|null $id = null,
        public float|null $amount = null,
        public Carbon|null $date = null,
        public bool|null $active = null,
        public PaymentState|null $payment_status = null,
        public EmployeeDTO|null $employee = null,
        public string|null $description = null,
    ) {
    }
}
