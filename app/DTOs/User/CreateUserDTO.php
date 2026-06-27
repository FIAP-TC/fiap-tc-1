<?php

namespace App\DTOs\User;

/**
 * DTO de criação de usuário.
 *
 * Transporta dados validados do FormRequest para o UserService.
 * A senha chega em texto puro aqui; o hashing é responsabilidade
 * do Service antes de persistir no banco.
 */
class CreateUserDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly int    $roleId,
        public readonly bool   $status = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            password: $data['password'],
            roleId:   (int) $data['role_id'],
            status:   (bool) ($data['status'] ?? true),
        );
    }
}
