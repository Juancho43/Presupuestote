<?php

namespace App\DTOs\V1;

use Carbon\Carbon;
use Ramsey\Uuid\Type\Decimal;

readonly class PaymentDTO
{
    public function __construct(
        public int|null $id = null,
        public decimal|null $amount = null,
        public Carbon|null $date = null,
        public string|null $description = null,
        public string|null $payable_type = null,
        public int|null $payable_id = null,

    ) {
    }
}
