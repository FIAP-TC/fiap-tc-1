<?php

namespace App\Infrastructure\Persistence\Eloquent\User\Repositories;

use App\Domain\User\Entites\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\UserMapper;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $userModel,
    ) {}

    public function findAll(): array
    {
        $models = $this->userModel->with('role')->get();

        return $models
            ->map(fn (User $model) => UserMapper::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?UserEntity
    {
        $model = $this->userModel->with('role')->find($id);

        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findByUsername(string $username): ?UserEntity
    {
        $model = $this->userModel->with('role')->where('username', $username)->first();

        return $model ? UserMapper::toDomain($model) : null;
    }

    public function create(array $data): UserEntity
    {
        $model = $this->userModel->create($data);
        $model->load('role');

        return UserMapper::toDomain($model);
    }

    public function update(int $id, array $data): ?UserEntity
    {
        $model = $this->userModel->find($id);

        if (!$model) {
            return null;
        }

        $model->update($data);
        $model->load('role');

        return UserMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->userModel->where('id', $id)->delete();
    }
}
