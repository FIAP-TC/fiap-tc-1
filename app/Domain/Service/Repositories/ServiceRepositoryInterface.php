<?php

namespace App\Domain\Service\Repositories;

use App\Domain\Service\Entites\ServiceEntity;

interface ServiceRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?ServiceEntity;
    public function findByIdIgnoringStatus(int $id): ?ServiceEntity;
    public function findManyByIds(array $ids): array;
    public function create(array $data): ServiceEntity;
    public function update(int $id, array $data): ?ServiceEntity;
    public function delete(int $id): bool;
}
