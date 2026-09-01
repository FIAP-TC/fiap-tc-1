<?php

namespace App\Domain\ServiceOrder\Entites;

/**
 * Representa um produto ou serviço cobrado dentro de uma Ordem de Serviço.
 *
 * O valor cobrado é o snapshot registrado no momento da inserção do item
 * (preserva o histórico de preços mesmo que o produto/serviço mude de valor depois).
 */
class ServiceOrderItemEntity
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly float $chargedValue,
        private readonly ?string $type = null,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getChargedValue(): float { return $this->chargedValue; }
    public function getType(): ?string { return $this->type; }
}
