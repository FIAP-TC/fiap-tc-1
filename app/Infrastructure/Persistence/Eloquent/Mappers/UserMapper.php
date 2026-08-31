<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\User\Entites\UserEntity;
use App\Domain\User\Entites\UserRoleEntity;
use App\Models\User;

class UserMapper
{
    public static function toDomain(User $model): UserEntity
    {
        $role = null;
        if ($model->relationLoaded('role') && $model->role) {
            $role = new UserRoleEntity(
                id: $model->role->id,
                name: $model->role->name,
            );
        }

        return new UserEntity(
            id: $model->id,
            username: $model->username,
            roleId: (int) $model->role_id,
            status: (bool) $model->status,
            role: $role,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}
