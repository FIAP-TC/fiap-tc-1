<?php

namespace App\Domain\Service\Entites;

use DateTimeInterface;

/**
 * Entidade de domínio para Serviço da Mecânica.
 *
 * Representa um serviço (ex.: troca de óleo, alinhamento) no domínio
 * da aplicação, sem dependência do Eloquent. Carrega apenas os dados
 * necessários para as regras de negócio centrais.
 */
class ServiceEntity
{
    public function __construct(
        private readonly ?int    $id,
        private readonly string  $name,
        private readonly float   $value,
        private readonly bool    $status = true,
        private readonly ?DateTimeInterface $createdAt = null,
        private readonly ?DateTimeInterface $modifiedDate = null,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getValue(): float { return $this->value; }
    public function isActive(): bool { return $this->status; }
    public function getCreatedAt(): ?DateTimeInterface { return $this->createdAt; }
    public function getModifiedDate(): ?DateTimeInterface { return $this->modifiedDate; }

    /**
     * Verifica se o valor do serviço é válido (não pode ser zero ou negativo).
     */
    public function hasValidValue(): bool
    {
        return $this->value > 0;
    }
}
