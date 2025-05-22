<?php

namespace App\DTOs\V1;

use App\States\PaymentState\PaymentState;
use Carbon\Carbon;

readonly class InvoiceDTO
{
    public function __construct(
        public int|null $id = null,
        public Carbon|null $date = null,
        public float|null  $total = null,
        public PaymentState|null  $payment_status = null,
        public SupplierDTO|null $supplier = null,
    ) {
    }
}
