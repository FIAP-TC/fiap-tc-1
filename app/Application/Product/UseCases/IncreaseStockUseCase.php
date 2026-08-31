<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class IncreaseStockUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, int $quantity): ProductEntity
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw ProductNotFoundException::withId($id);
        }

        $updatedProduct = $product->addStockValue($quantity);

        $product = $this->productRepository->update(
            id: $id,
            data: [
                'quantity' => $updatedProduct->getQuantity(),
                'modified_date' => now(),
            ]
        );

        if (!$product) {
            throw ProductNotFoundException::withId($id);
        }

        return $product;
    }
}