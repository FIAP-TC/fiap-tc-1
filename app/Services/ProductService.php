<?php

namespace App\Services;

use App\DTOs\Product\ProductDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function findAll(): Collection
    {
        return $this->productRepository->findAll();
    }

    public function findById(int $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new \RuntimeException('Product not found.');
        }

        return $product;
    }

    public function create(ProductDTO $dto): Product
    {
        return $this->productRepository->create($dto->toArray());
    }


    public function update(int $id, ProductDTO $dto): void
    {
        if (!$this->productRepository->update($id, $dto->toArray())) {
            throw new \RuntimeException('Product not found.');
        }
    }

    public function delete(int $id): void
    {
        if (!$this->productRepository->delete($id)) {
            throw new \RuntimeException('Product not found.');
        }
    }

    public function increaseStock(int $id, int $quantity): void
    {
        $product = $this->findById($id);

        $this->productRepository->update($id, [
            'quantity' => $product->quantity + $quantity,
            'modified_date' => now(),
        ]);
    }

    public function decreaseStock(int $id, int $quantity): void
    {
        $product = $this->findById($id);
        if ($product->quantity < $quantity) {
            throw new \RuntimeException('Insufficient stock.');
        }

        $this->productRepository->update($id, [
            'quantity' => $product->quantity - $quantity,
            'modified_date' => now(),
        ]);
    }
}
