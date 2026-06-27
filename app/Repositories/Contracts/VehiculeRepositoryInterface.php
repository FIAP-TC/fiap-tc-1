<?php

namespace App\Repositories\Contracts;

use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Collection;

interface VehiculeRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Vehicule;
    public function findByIdIgnoringStatus(int $id): ?Vehicule;
    public function findByCustomer(int $customerId): Collection;
    public function create(array $data): Vehicule;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
