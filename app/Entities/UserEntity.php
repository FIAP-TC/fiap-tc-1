<?php

namespace App\Entities;

/**
 * Entidade de domínio para User.
 *
 * Representa o usuário no domínio da aplicação, independente de
 * infraestrutura (banco, ORM). Contém comportamentos de domínio
 * que não pertencem ao Model Eloquent nem ao Service.
 *
 * A senha nunca é exposta publicamente — use getPassword() apenas
 * internamente para persistência.
 */
class UserEntity
{
    public function __construct(
        private readonly ?int   $id,
        private readonly string $username,
        private readonly string $password,
        private readonly int    $roleId,
        private readonly bool   $status = true,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /** Retorna o hash da senha — uso restrito à camada de persistência */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Cria uma nova instância com status inativo.
     * Usamos imutabilidade para manter a entidade previsível.
     */
    public function deactivate(): self
    {
        return new self(
            $this->id,
            $this->username,
            $this->password,
            $this->roleId,
            false,
        );
    }
}
