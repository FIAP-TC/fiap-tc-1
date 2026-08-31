<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Entites\UserEntity;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final class UpdateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id, UserDTO $data): UserEntity
    {
        $attributes = $data->toArray();

        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $attributes['modified_date'] = now()->toDateTimeString();

        $user = $this->userRepository->update($id, $attributes);

        if (!$user) {
            throw UserNotFoundException::withId($id);
        }

        return $user;
    }
}
