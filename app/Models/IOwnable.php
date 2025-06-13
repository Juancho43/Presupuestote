<?php

namespace App\Models;

use App\States\PaymentState\PaymentState;
use Carbon\Carbon;

interface IOwnable {

    public function getPaymentStatus(): PaymentState;
    public function getDate(): Carbon;
    public function getTotal(): float;
}
