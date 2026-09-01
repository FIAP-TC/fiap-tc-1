<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Entites\UserEntity;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final class FindUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id): UserEntity
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw UserNotFoundException::withId($id);
        }

        return $user;
    }
}
