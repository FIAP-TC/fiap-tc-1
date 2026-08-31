<?php

namespace App\Domain\ServiceOrder\Entites;

/**
 * Representa um status possível do fluxo da Ordem de Serviço
 * (ex.: "Recebida", "Em diagnóstico").
 */
class ServiceOrderStatusEntity
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}
