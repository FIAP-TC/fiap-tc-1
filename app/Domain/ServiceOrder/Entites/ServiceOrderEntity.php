<?php

namespace App\Domain\ServiceOrder\Entites;

use App\Domain\Vehicule\Entites\VehiculeEntity;
use DateTimeInterface;

/**
 * Entidade de domínio para Ordem de Serviço.
 *
 * As constantes de status abaixo evitam que os IDs de
 * service_order_status fiquem hardcoded fora da camada de domínio.
 */
class ServiceOrderEntity
{
    public const STATUS_RECEBIDA               = 1;
    public const STATUS_EM_DIAGNOSTICO         = 2;
    public const STATUS_AGUARDANDO_APROVACAO   = 3;
    public const STATUS_EM_EXECUCAO            = 4;
    public const STATUS_FINALIZADA             = 5;
    public const STATUS_ENTREGUE               = 6;
    public const STATUS_APROVADA_PELO_CLIENTE  = 7;
    public const STATUS_REPROVADA_PELO_CLIENTE = 8;

    /**
     * @param ServiceOrderItemEntity[] $products
     * @param ServiceOrderItemEntity[] $services
     * @param ServiceOrderStatusHistoryEntryEntity[] $statusHistory
     */
    public function __construct(
        private readonly ?int $id,
        private readonly int $usersId,
        private readonly int $usersRoleId,
        private readonly int $vehiculesId,
        private readonly float $orderValue,
        private readonly ?float $timeAverage,
        private readonly bool $status = true,
        private readonly ?VehiculeEntity $vehicule = null,
        private readonly array $products = [],
        private readonly array $services = [],
        private readonly array $statusHistory = [],
        private readonly ?ServiceOrderStatusEntity $currentStatus = null,
        private readonly ?DateTimeInterface $createdAt = null,
        private readonly ?DateTimeInterface $modifiedDate = null,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getUsersId(): int { return $this->usersId; }
    public function getUsersRoleId(): int { return $this->usersRoleId; }
    public function getVehiculesId(): int { return $this->vehiculesId; }
    public function getOrderValue(): float { return $this->orderValue; }
    public function getTimeAverage(): ?float { return $this->timeAverage; }
    public function isActive(): bool { return $this->status; }
    public function getVehicule(): ?VehiculeEntity { return $this->vehicule; }
    public function getProducts(): array { return $this->products; }
    public function getServices(): array { return $this->services; }
    public function getStatusHistory(): array { return $this->statusHistory; }
    public function getCurrentStatus(): ?ServiceOrderStatusEntity { return $this->currentStatus; }
    public function getCreatedAt(): ?DateTimeInterface { return $this->createdAt; }
    public function getModifiedDate(): ?DateTimeInterface { return $this->modifiedDate; }
}
