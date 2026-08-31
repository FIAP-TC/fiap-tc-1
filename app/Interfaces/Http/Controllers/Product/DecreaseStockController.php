<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\UseCases\DecreaseStockUseCase;
use App\Application\Product\UseCases\FindProductUseCase;
use App\Interfaces\Http\Requests\Product\UpdateProductStockRequest;
use App\Interfaces\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class DecreaseStockController
{

    public function __invoke(
        int $id,
        UpdateProductStockRequest $request,
        DecreaseStockUseCase $useCase,
    ): JsonResponse {
        $quantityValidated = $request->validated()['quantity'];
        $products = $useCase->execute($id, $quantityValidated);
        return ProductResource::make($products)->response();
    }
}
