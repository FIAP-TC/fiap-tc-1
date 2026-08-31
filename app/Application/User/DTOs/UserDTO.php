<?php

namespace App\Application\User\DTOs;

/**
 * DTO único para criação e atualização de Usuário.
 *
 * Todos os campos são nullable para suportar atualizações parciais (PATCH-like).
 */
class UserDTO
{
    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?int $roleId = null,
        public readonly ?bool $status = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            roleId: isset($data['role_id']) ? (int) $data['role_id'] : null,
            status: isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    /**
     * Serializa apenas os campos preenchidos, excluindo nulos.
     * A senha já deve chegar hasheada quando presente.
     */
    public function toArray(): array
    {
        return array_filter([
            'username' => $this->username,
            'password' => $this->password,
            'role_id'  => $this->roleId,
            'status'   => $this->status,
        ], fn ($v) => $v !== null);
    }
}
