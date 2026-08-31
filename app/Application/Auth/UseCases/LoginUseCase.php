<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTOs\LoginDTO;
use App\DTOs\Auth\LoginDTO as LegacyLoginDTO;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Services\AuthService;

final class LoginUseCase
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(LoginDTO $dto): string
    {
        try {
            return $this->authService->login(new LegacyLoginDTO(
                username: $dto->username,
                password: $dto->password,
            ));
        } catch (\RuntimeException) {
            throw InvalidCredentialsException::make();
        }
    }
}