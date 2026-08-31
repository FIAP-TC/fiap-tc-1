<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Service\Entites\ServiceEntity;
use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;

class ServiceMapper
{
    public static function toDomain(Service $model): ServiceEntity
    {
        return new ServiceEntity(
            id: $model->id,
            name: $model->name,
            value: (float) $model->value,
            status: (bool) $model->status,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}
