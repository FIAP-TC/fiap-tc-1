<?php

namespace App\Entities;

/**
 * Entidade de domínio para Customer.
 *
 * Encapsula as regras de negócio relacionadas ao cliente,
 * como os tipos de identificação aceitos (CPF/CNPJ) e
 * comportamentos de domínio independentes de infraestrutura.
 *
 * Nota sobre identification_number:
 * O campo é INT no banco (schema original do Workbench), o que implica
 * perda de zeros à esquerda em CPFs como 01234567890. Para produção,
 * recomenda-se migrar para VARCHAR(14). Ver melhorias futuras.
 */
class CustomerEntity
{
    public const IDENTIFICATION_CPF  = 'CPF';
    public const IDENTIFICATION_CNPJ = 'CNPJ';

    public const CPF_LENGTH  = 11;
    public const CNPJ_LENGTH = 14;

    public function __construct(
        private readonly ?int   $id,
        private readonly string $name,
        private readonly string $identification,
        private readonly int    $identificationNumber,
        private readonly string $email,
        private readonly bool   $status = true,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getIdentification(): string { return $this->identification; }
    public function getIdentificationNumber(): int { return $this->identificationNumber; }
    public function getEmail(): string { return $this->email; }
    public function isActive(): bool { return $this->status; }

    public function isCpf(): bool
    {
        return $this->identification === self::IDENTIFICATION_CPF;
    }

    public function isCnpj(): bool
    {
        return $this->identification === self::IDENTIFICATION_CNPJ;
    }

    /**
     * Retorna o número de dígitos esperado baseado no tipo de identificação.
     * Útil para validações adicionais na camada de domínio.
     */
    public function expectedIdentificationLength(): int
    {
        return $this->isCpf() ? self::CPF_LENGTH : self::CNPJ_LENGTH;
    }
}
