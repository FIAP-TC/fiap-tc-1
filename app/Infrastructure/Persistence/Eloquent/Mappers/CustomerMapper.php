<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Customer\Entites\CustomerEntity;
use App\Infrastructure\Persistence\Eloquent\Customer\Models\Customer;
use App\Models\Vehicule as VehiculeModel;

class CustomerMapper
{
    public static function toDomain(Customer $model): CustomerEntity
    {
        $vehicles = [];

        if ($model->relationLoaded('vehicules') && $model->vehicules) {
            $vehicles = $model->vehicules
                ->map(fn (VehiculeModel $vehicleModel) => VehiculeMapper::toDomain($vehicleModel))
                ->all();
        }

        return new CustomerEntity(
            id: $model->id,
            name: $model->name,
            identification: $model->identification,
            identificationNumber: (int) $model->identification_number,
            email: $model->email,
            status: (bool) $model->status,
            vehicles: $vehicles,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}