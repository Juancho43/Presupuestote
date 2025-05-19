<?php

namespace App\DTOs\V1;

use App\States\PaymentState\PaymentState;
use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class SalaryDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null $amount = null,
        public Date|null $date = null,
        public bool|null $active = null,
        public PaymentState|null $payment_status = null,
        public EmployeeDTO|null $employee = null,
    ) {
    }
}
