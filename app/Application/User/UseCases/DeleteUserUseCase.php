<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final class DeleteUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id): bool
    {
        if (!$this->userRepository->findById($id)) {
            throw UserNotFoundException::withId($id);
        }

        return $this->userRepository->delete($id);
    }
}
