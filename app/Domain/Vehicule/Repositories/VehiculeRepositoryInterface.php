<?php

namespace App\Domain\Vehicule\Repositories;

use App\Domain\Vehicule\Entites\VehiculeEntity;

interface VehiculeRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?VehiculeEntity;
    public function findByIdIgnoringStatus(int $id): ?VehiculeEntity;
    public function findByCustomer(int $customerId): ?array;
    public function create(array $data): VehiculeEntity;
    public function update(int $id, array $data): ?VehiculeEntity;
    public function delete(int $id): bool;
}
