<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Infrastructure\Auth\AuthService;

final class LoginUseCase
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(LoginDTO $dto): string
    {
        try {
            return $this->authService->login($dto);
        } catch (\RuntimeException) {
            throw InvalidCredentialsException::make();
        }
    }
}