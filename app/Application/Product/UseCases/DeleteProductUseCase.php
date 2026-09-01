<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id): bool
    {
        $deleted = $this->productRepository->delete($id);

        if (!$deleted) {
            throw ProductNotFoundException::withId($id);
        }

        return true;
    }
}