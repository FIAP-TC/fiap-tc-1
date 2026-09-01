<?php

namespace App\Application\Auth\UseCases;

use App\Infrastructure\Auth\AuthService;

final class RefreshUseCase
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(): string
    {
        return $this->authService->refresh();
    }
}