<?php
namespace App\Enums;

enum WorkStatus: string
{
    case PRESUPUESTADO = 'Presupuestado';
    case PENDIENTE_DE_APROBACION = 'Pendiente de aprobación';
    case APROBADO = 'Aprobado';
    case EN_PROCESO = 'En proceso';
    case ENTREGADO = 'Entregado';
    case CANCELADO = 'Cancelado';
}
