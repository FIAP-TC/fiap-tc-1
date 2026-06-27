<?php

namespace App\Entities;

/**
 * Entidade de domínio para Vehicule.
 *
 * Representa o veículo no domínio da aplicação, carregando
 * o id do cliente proprietário para suportar regras de associação
 * sem depender do Eloquent diretamente.
 */
class VehiculeEntity
{
    public function __construct(
        private readonly ?int   $id,
        private readonly string $name,
        private readonly string $plate,
        private readonly string $model,
        private readonly string $brand,
        private readonly int    $years,
        private readonly int    $customerId,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPlate(): string { return $this->plate; }
    public function getModel(): string { return $this->model; }
    public function getBrand(): string { return $this->brand; }
    public function getYears(): int { return $this->years; }
    public function getCustomerId(): int { return $this->customerId; }

    /**
     * Verifica se este veículo pertence ao cliente informado.
     * Usado para validar associações antes de operações sensíveis.
     */
    public function belongsToCustomer(int $customerId): bool
    {
        return $this->customerId === $customerId;
    }
}
