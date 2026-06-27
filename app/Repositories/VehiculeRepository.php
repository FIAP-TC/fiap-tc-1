<?php

namespace App\Repositories;

use App\Models\Vehicule;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VehiculeRepository implements VehiculeRepositoryInterface
{
    public function findAll(): Collection
    {
        return Vehicule::with('customer')->where('status', true)->get();
    }

    public function findById(int $id): ?Vehicule
    {
        return Vehicule::with('customer')->where('status', true)->find($id);
    }

    public function findByIdIgnoringStatus(int $id): ?Vehicule
    {
        return Vehicule::with('customer')->find($id);
    }

    public function findByCustomer(int $customerId): Collection
    {
        return Vehicule::with('customer')
            ->where('customer_id', $customerId)
            ->where('status', true)
            ->get();
    }

    public function create(array $data): Vehicule
    {
        return Vehicule::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) Vehicule::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Vehicule::where('id', $id)->update(['status' => false]);
    }
}
