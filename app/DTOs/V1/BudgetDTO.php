<?php

namespace App\DTOs\V1;

use App\States\BudgetState\BudgetState;
use App\States\PaymentState\PaymentState;
use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Type\Decimal;

readonly class BudgetDTO
{
    public function __construct(
        public int|null $id = null,
        public string|null $description = null,
        public Date|null $madeDate = null,
        public Date|null $deadLine = null,
        public decimal|null $cost = null,
        public decimal|null $profit = null,
        public decimal|null  $price = null,
        public BudgetState|null $status = null,
        public PaymentState|null $paymentState = null,
        public ClientDTO|null $client = null
    ) {
    }
}
