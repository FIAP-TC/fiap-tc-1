<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Entites\CustomerEntity;

interface CustomerRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?CustomerEntity;
    public function findByIdIgnoringStatus(int $id): ?CustomerEntity;
    public function create(array $data): CustomerEntity;
    public function update(int $id, array $data): ?CustomerEntity;
    public function delete(int $id): bool;
}
