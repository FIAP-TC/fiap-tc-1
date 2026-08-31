<?php

namespace App\Domain\ServiceOrder\Entites;

use DateTimeInterface;

/**
 * Representa uma transição de status registrada no histórico da Ordem de Serviço.
 */
class ServiceOrderStatusHistoryEntryEntity
{
    public function __construct(
        private readonly ServiceOrderStatusEntity $status,
        private readonly DateTimeInterface $createdAt,
    ) {}

    public function getStatus(): ServiceOrderStatusEntity { return $this->status; }
    public function getCreatedAt(): DateTimeInterface { return $this->createdAt; }
}
