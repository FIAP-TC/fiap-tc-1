<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class FindProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id): ProductEntity
    {
        return $this->productRepository->findById($id);
    }
}