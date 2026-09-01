<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Entites\ProductEntity;

interface ProductRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?ProductEntity;
    public function findManyByIds(array $ids): array;
    public function create(array $data): ProductEntity;
    public function update(int $id, array $data): ?ProductEntity;
    public function delete(int $id): bool;
}
