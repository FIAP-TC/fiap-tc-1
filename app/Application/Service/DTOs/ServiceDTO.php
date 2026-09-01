<?php

namespace App\Application\Service\DTOs;

/**
 * DTO único para criação e atualização de Serviço.
 *
 * Todos os campos são nullable para suportar atualizações parciais (PATCH-like).
 * O Service injeta os defaults de criação (status, create_date) via array_merge,
 * garantindo que o DTO não carregue responsabilidade de negócio.
 */
class ServiceDTO
{
    public function __construct(
        public readonly ?string $name   = null,
        public readonly ?float  $value  = null,
        public readonly ?bool   $status = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:   $data['name'] ?? null,
            value:  isset($data['value']) ? (float) $data['value'] : null,
            status: isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    /**
     * Serializa apenas os campos preenchidos, excluindo nulos.
     * Usado no update para não sobrescrever campos não enviados.
     */
    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'value'         => $this->value,
            'status'        => $this->status,
        ], fn($v) => $v !== null);
    }
}
