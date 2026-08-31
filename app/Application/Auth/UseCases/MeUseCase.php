<?php

namespace App\Application\Auth\UseCases;

use App\Domain\User\Entites\UserEntity;
use App\Infrastructure\Persistence\Eloquent\Mappers\UserMapper;
use App\Services\AuthService;

final class MeUseCase
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(): UserEntity
    {
        $user = $this->authService->me();
        $user->load('role');

        return UserMapper::toDomain($user);
    }
}