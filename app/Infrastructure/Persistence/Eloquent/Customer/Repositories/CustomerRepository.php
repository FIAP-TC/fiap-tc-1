<?php

namespace App\Infrastructure\Persistence\Eloquent\Customer\Repositories;

use App\Domain\Customer\Entites\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Customer\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Mappers\CustomerMapper;

final class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(
        private readonly Customer $customerModel,
    ) {}

    public function findAll(): array
    {
        $models = $this->customerModel
            ->with('vehicules')
            ->where('status', true)
            ->get();

        return $models
            ->map(fn(Customer $model) => CustomerMapper::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?CustomerEntity
    {
        $model = $this->customerModel
            ->with('vehicules')
            ->where('status', true)
            ->find($id);

        return $model ? CustomerMapper::toDomain($model) : null;
    }

    public function findByIdIgnoringStatus(int $id): ?CustomerEntity
    {
        $model = $this->customerModel
            ->with('vehicules')
            ->find($id);

        return $model ? CustomerMapper::toDomain($model) : null;
    }

    public function create(array $data): CustomerEntity
    {
        $model = $this->customerModel->create($data);

        return CustomerMapper::toDomain($model);
    }

    public function update(int $id, array $data): ?CustomerEntity
    {
        $model = $this->customerModel->find($id);
        if (!$model) {
            return null;
        }

        $model->update($data);
        return CustomerMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->customerModel
            ->where('id', $id)
            ->update(['status' => false]);
    }
}
