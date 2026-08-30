<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\UseCases\ListProductUseCase;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class ListProductsController
{
    /**
     * @api {get} /api/products List products
     * @apiName ListProducts
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        ListProductUseCase $useCase, 
    ): JsonResponse
    {
        $products = $useCase->execute();
        return ProductResource::collection($products)->response();
    }
}