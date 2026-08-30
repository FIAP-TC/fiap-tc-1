<?php

namespace App\Infrastructure\Persistence\Eloquent\Product\Repositories;

use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\ProductMapper;
use App\Infrastructure\Persistence\Eloquent\Product\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $productModel,
    ) {}

    public function findAll(): array
    {
        $models = $this->productModel
            ->where('status', true)
            ->get();

        return $models
            ->map(fn(Product $model) => ProductMapper::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?ProductEntity
    {
        $model = $this->productModel
            ->where('status', true)
            ->find($id);

        return $model ? ProductMapper::toDomain($model) : null;
    }

    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $models = $this->productModel
            ->whereIn('id', $ids)
            ->where('status', true)
            ->get();

        return $models
            ->map(fn(Product $model) => ProductMapper::toDomain($model))
            ->all();
    }

    public function create(array $data): ProductEntity
    {
        $model = $this->productModel->create($data);
        return ProductMapper::toDomain($model);
    }

    public function update(int $id, array $data): ?ProductEntity
    {
        $model = $this->productModel->find($id);

        if (!$model) {
            return null;
        }

        $model->update($data);

        return ProductMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        $model = $this->productModel->find($id);

        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }
}
