<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class FindProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id): ProductEntity
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw ProductNotFoundException::withId($id);
        }

        return $product;
    }
}