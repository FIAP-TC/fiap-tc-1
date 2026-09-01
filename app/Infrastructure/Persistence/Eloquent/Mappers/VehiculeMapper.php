<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;

class VehiculeMapper
{
    public static function toDomain(Vehicule $model): VehiculeEntity
    {
        $customerEntity = null;

        if ($model->relationLoaded('customer') && $model->customer) {
            $customerEntity = CustomerMapper::toDomain($model->customer);
        }

        return new VehiculeEntity(
            id: $model->id,
            name: $model->name,
            plate: $model->plate,
            model: $model->model,
            brand: $model->brand ?? null,
            years: (int) $model->years,
            customerId: (int) $model->customer_id,
            status: (int) $model->status,
            customer: $customerEntity,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}
