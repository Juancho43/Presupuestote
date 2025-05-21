<?php

namespace App\States\PaymentState;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PaymentState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Deuda::class)
            ->allowTransition(Deuda::class, Pago::class)
            ;
    }
}
