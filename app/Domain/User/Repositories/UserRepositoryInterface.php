<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entites\UserEntity;

interface UserRepositoryInterface
{
    /** @return UserEntity[] */
    public function findAll(): array;

    public function findById(int $id): ?UserEntity;
    public function findByUsername(string $username): ?UserEntity;
    public function create(array $data): UserEntity;
    public function update(int $id, array $data): ?UserEntity;
    public function delete(int $id): bool;
}
