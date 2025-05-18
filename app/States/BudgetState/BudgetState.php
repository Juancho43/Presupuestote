<?php

namespace App\States\BudgetState;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class BudgetState extends State
{
    public static function config() : StateConfig
    {
        return parent::config()
            ->default(Presupuestado::class)
            ->allowTransition(Presupuestado::class, Aprobado::class)
            ->allowTransition([Aprobado::class, Rechazado::class], EnProceso::class)
            ->allowTransition([Presupuestado::class, Aprobado::class], Rechazado::class)
            ->allowTransition([Presupuestado::class, Aprobado::class, EnProceso::class, Rechazado::class], Cancelado::class)
            ->allowTransition(EnProceso::class,Entregado::class)
            ->allowTransition(Cancelado::class, Aprobado::class)
            ;
    }
}
