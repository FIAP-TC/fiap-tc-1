<?php

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\ProductDTO;
use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Product\Exceptions\ProductCreationFailedException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(ProductDTO $data): ProductEntity
    {
        $product = $this->productRepository->create($data->toArray());

        if (!$product) {
            throw ProductCreationFailedException::create();
        }

        return $product;
    }
}