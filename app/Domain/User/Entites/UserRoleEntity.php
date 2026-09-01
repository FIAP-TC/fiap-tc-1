<?php

namespace App\Domain\User\Entites;

/**
 * Representa a role (perfil de acesso) associada a um usuário.
 */
class UserRoleEntity
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}
