<?php

namespace App\States\WorkState;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

class WorkState extends State
{
    public static function config() : StateConfig
    {
        return parent::config()
            ->default(Presupuestado::class)
            ->allowTransition(Presupuestado::class,Aprobado::class)
            ->allowTransition(Aprobado::class,Elaborando::class)
            ->allowTransition(Elaborando::class,Entregado::class)
            ->allowTransition([Presupuestado::class,Aprobado::class, Elaborando::class],Cancelado::class)
            ->allowTransition(Cancelado::class,Aprobado::class)
            ;
    }
}
