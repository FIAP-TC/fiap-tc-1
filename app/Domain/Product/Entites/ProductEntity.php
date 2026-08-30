<?php

namespace App\Domain\Product\Entities;

use DateTimeInterface;

/**
 * Entidade de domínio para Product.
 *
 * Encapsula o estado e os comportamentos de negócio
 * relacionados ao produto, mantendo a camada de domínio
 * independente do Eloquent.
 */
class ProductEntity
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $type,
        private readonly float $value,
        private readonly int $quantity,
        private readonly bool $status = true,
        private readonly ?DateTimeInterface $createdAt = null,
        private readonly ?DateTimeInterface $modifiedDate = null,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getValue(): float { return $this->value; }
    public function getQuantity(): int { return $this->quantity; }
    public function isActive(): bool { return $this->status; }
    public function getCreatedAt(): ?DateTimeInterface { return $this->createdAt; }
    public function getModifiedDate(): ?DateTimeInterface { return $this->modifiedDate; }

    /**
     * Verifica se o produto tem estoque disponível.
     */
    public function hasStock(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Calcula o valor total do estoque deste produto.
     */
    public function getTotalStockValue(): float
    {
        return $this->value * $this->quantity;
    }
}