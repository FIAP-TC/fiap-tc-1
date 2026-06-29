<?php

namespace App\Enums;

enum ServiceOrderStatusEnum: int
{
    case RECEIVED = 1;
    case IN_DIAGNOSIS = 2;
    case WAITING_APPROVAL = 3;
    case IN_EXECUTION = 4;
    case FINISHED = 5;
    case DELIVERED = 6;

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Recebida',
            self::IN_DIAGNOSIS => 'Em diagnóstico',
            self::WAITING_APPROVAL => 'Aguardando aprovação',
            self::IN_EXECUTION => 'Em execução',
            self::FINISHED => 'Finalizada',
            self::DELIVERED => 'Entregue',
        };
    }
}