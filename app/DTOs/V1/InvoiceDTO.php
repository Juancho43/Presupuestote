<?php

namespace App\DTOs\V1;

use App\States\PaymentState\PaymentState;
use Carbon\Carbon;
use Ramsey\Uuid\Type\Decimal;

readonly class InvoiceDTO
{
    public function __construct(
        public int|null $id = null,
        public Carbon|null $date = null,
        public decimal|null  $total = null,
        public PaymentState|null  $payment_status = null,
        public SupplierDTO|null $supplier = null,
    ) {
    }
}
