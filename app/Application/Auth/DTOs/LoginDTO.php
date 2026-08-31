<?php

namespace App\Application\Auth\DTOs;

/**
 * DTO de login.
 *
 * Transporta as credenciais do Controller para o LoginUseCase sem
 * expor o objeto Request diretamente à camada de aplicação.
 */
class LoginDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            password: $data['password'],
        );
    }
}