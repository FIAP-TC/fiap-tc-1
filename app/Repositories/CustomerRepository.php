<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findAll(): Collection
    {
        return Customer::with('vehicules')->where('status', true)->get();
    }

    public function findById(int $id): ?Customer
    {
        return Customer::with('vehicules')->where('status', true)->find($id);
    }

    public function findByIdIgnoringStatus(int $id): ?Customer
    {
        return Customer::with('vehicules')->find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) Customer::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Customer::where('id', $id)->update(['status' => false]);
    }
}
