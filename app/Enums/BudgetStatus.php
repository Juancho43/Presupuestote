<?php

namespace App\Enums;

enum BudgetStatus: string
{

    case PRESUPUESTADO = 'Presupuestado';
    case APROBADO = 'Aprobado';
    case RECHAZADO = 'Rechazado';
    case EN_PROCESO = 'En proceso';
    case ENTREGADO = 'Entregado';
    case CANCELADO = 'Cancelado';
}
