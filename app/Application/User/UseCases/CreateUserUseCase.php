<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Entites\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(UserDTO $data): UserEntity
    {
        return $this->userRepository->create([
            'username'      => $data->username,
            'password'      => Hash::make((string) $data->password),
            'role_id'       => $data->roleId,
            'status'        => $data->status ?? true,
            'create_date'   => now()->toDateTimeString(),
        ]);
    }
}
