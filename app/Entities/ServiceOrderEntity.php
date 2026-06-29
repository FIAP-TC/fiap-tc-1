<?php

namespace App\Entities;

/**
 * Entidade de domínio para Ordem de Serviço.
 *
 * Define as constantes de status inicial para garantir que o ID 1 ("Recebida")
 * nunca fique hardcoded fora da camada de domínio.
 *
 * Qualquer novo status inserido via ServiceOrderStatusSeeder deve ter sua
 * constante correspondente declarada aqui.
 */
class ServiceOrderEntity
{
    /** ID do status inicial obrigatório de toda nova Ordem de Serviço. */
    public const STATUS_RECEBIDA             = 1;
    public const STATUS_EM_DIAGNOSTICO       = 2;
    public const STATUS_AGUARDANDO_APROVACAO = 3;
    public const STATUS_EM_EXECUCAO          = 4;
    public const STATUS_FINALIZADA           = 5;
    public const STATUS_ENTREGUE             = 6;

    public function __construct(
        private readonly ?int   $id,
        private readonly int    $usersId,
        private readonly int    $vehiculesId,
        private readonly float  $orderValue,
        private readonly bool   $status = true,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getUsersId(): int { return $this->usersId; }
    public function getVehiculesId(): int { return $this->vehiculesId; }
    public function getOrderValue(): float { return $this->orderValue; }
    public function isActive(): bool { return $this->status; }
}
