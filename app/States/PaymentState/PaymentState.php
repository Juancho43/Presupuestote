<?php

namespace App\States\PaymentState;

use App\States\BudgetState\Aprobado;
use App\States\BudgetState\Presupuestado;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PaymentState extends State
{
    public static function config() : StateConfig
    {
        return parent::config()
            ->default(Deuda::class)
            ->allowTransition(Deuda::class, Pago::class)
            ;
    }
}
