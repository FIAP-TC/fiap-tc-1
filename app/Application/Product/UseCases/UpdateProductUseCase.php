<?php

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\ProductDTO;
use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, ProductDTO $data): ProductEntity
    {
        $product = $this->productRepository->update($id, $data->toArray());
        
        if (!$product) {
            throw ProductNotFoundException::withId($id);
        }

        return $product;
    }
}