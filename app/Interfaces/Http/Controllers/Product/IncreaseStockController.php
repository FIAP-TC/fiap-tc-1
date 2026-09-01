<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\UseCases\IncreaseStockUseCase;
use App\Interfaces\Http\Requests\Product\UpdateProductStockRequest;
use App\Interfaces\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class IncreaseStockController
{
    public function __invoke(
        int $id,
        UpdateProductStockRequest $request,
        IncreaseStockUseCase $useCase,
    ): JsonResponse {
        $quantityValidated = $request->validated()['quantity'];
        $product = $useCase->execute($id, $quantityValidated);
        return ProductResource::make($product)->response();
    }
}