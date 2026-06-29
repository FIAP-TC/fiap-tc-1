<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $productModel,
    ) {}

    public function findAll(): Collection
    {
        return $this->productModel->all();
    }

    public function findById(int $id): ?Product
    {
        return $this->productModel->find($id);
    }

    public function findManyByIds(array $ids): Collection
    {
        return Product::whereIn('id', $ids)->get();
    }

    public function create(array $data): Product
    {
        return $this->productModel->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return (bool) $product->delete();
    }
}
