<?php

namespace App\Infrastructure\Persistence\Eloquent\Vehicule\Repositories;

use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\VehiculeMapper;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;

final class VehicleRepository implements VehiculeRepositoryInterface
{
    public function __construct(
        private readonly Vehicule $vehiculeModel,
    ) {}

    public function findAll(): array
    {
        $models = $this->vehiculeModel
            ->with('customer')
            ->where('status', true)
            ->get();
        
        return $models
            ->map(fn(Vehicule $model) => VehiculeMapper::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?VehiculeEntity
    {
        $model = $this->vehiculeModel
            ->with('customer')
            ->where('status', true)
            ->find($id);

        return $model ? VehiculeMapper::toDomain($model) : null;
    }

    public function findByIdIgnoringStatus(int $id): ?VehiculeEntity
    {
        $model = $this->vehiculeModel
            ->with('customer')
            ->find($id);
        
        return $model ? VehiculeMapper::toDomain($model) : null;
    }

    public function findByCustomer(int $customerId): array
    {
        $models = $this->vehiculeModel
            ->with('customer')
            ->where('customer_id', $customerId)
            ->where('status', true)
            ->get();

        return $models
            ->map(fn (Vehicule $model) => VehiculeMapper::toDomain($model))
            ->all();
    }

    public function create(array $data): VehiculeEntity
    {
        $model = $this->vehiculeModel
            ->create($data);

        $model->load('customer');

        return VehiculeMapper::toDomain($model);
    }

    public function update(int $id, array $data): ?VehiculeEntity
    {
        $model = $this->vehiculeModel
            ->where('status', true)
            ->find($id);

        if (!$model) {
            return null;
        }

        $model->update($data);
        $model->load('customer');
        
        return VehiculeMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->vehiculeModel
            ->where('id', $id)
            ->update(['status' => false]);
    }
}
