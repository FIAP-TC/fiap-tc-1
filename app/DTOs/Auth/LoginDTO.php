<?php

namespace App\DTOs\Auth;

/**
 * DTO de login.
 *
 * Transporta as credenciais do Controller para o AuthService sem
 * expor o objeto Request diretamente à camada de negócio.
 * Isso permite testar o Service de forma isolada (sem HTTP).
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
