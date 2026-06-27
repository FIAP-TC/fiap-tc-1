<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(private readonly Role $model) {}

    public function findAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }
}
