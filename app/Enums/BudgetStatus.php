<?php

namespace App\Enums;

enum BudgetStatus: string
{
    case DRAFT      = 'draft';
    case SENT       = 'sent';
    case APPROVED   = 'approved';
    case CONVERTED  = 'converted';
    case REFUSED    = 'refused';
    case EXPIRED    = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Rascunho',
            self::SENT      => 'Enviado',
            self::APPROVED  => 'Aprovado',
            self::CONVERTED => 'Convertido',
            self::REFUSED   => 'Recusado',
            self::EXPIRED   => 'Expirado',
        };
    }
}
