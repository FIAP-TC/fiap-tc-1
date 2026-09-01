<?php

namespace App\Application\Auth\UseCases;

use App\Infrastructure\Auth\AuthService;

final class LogoutUseCase
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(): void
    {
        $this->authService->logout();
    }
}