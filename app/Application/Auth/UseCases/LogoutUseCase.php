<?php

namespace App\Application\Auth\UseCases;

use App\Services\AuthService;

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