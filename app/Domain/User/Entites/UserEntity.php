<?php

namespace App\Domain\User\Entites;

use DateTimeInterface;

/**
 * Entidade de domínio para Usuário.
 *
 * Não carrega a senha — isso é uma preocupação de autenticação/persistência,
 * não do domínio. Hash e verificação de senha ficam na camada de aplicação.
 */
class UserEntity
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $username,
        private readonly int $roleId,
        private readonly bool $status = true,
        private readonly ?UserRoleEntity $role = null,
        private readonly ?DateTimeInterface $createdAt = null,
        private readonly ?DateTimeInterface $modifiedDate = null,
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getRoleId(): int { return $this->roleId; }
    public function isActive(): bool { return $this->status; }
    public function getRole(): ?UserRoleEntity { return $this->role; }
    public function getCreatedAt(): ?DateTimeInterface { return $this->createdAt; }
    public function getModifiedDate(): ?DateTimeInterface { return $this->modifiedDate; }
}
