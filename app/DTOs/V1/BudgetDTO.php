<?php

namespace App\DTOs\V1;

use App\States\BudgetState\BudgetState;
use App\States\PaymentState\PaymentState;
use Carbon\Carbon;


readonly class BudgetDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $description = null,
        public Carbon|null $madeDate = null,
        public Carbon|null $deadLine = null,
        public float|null $cost = null,
        public float|null $profit = null,
        public float|null  $price = null,
        public BudgetState|null $status = null,
        public PaymentState|null $paymentState = null,
        public ClientDTO|null $client = null
    ) {
    }
}
