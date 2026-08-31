<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\UseCases\FindProductUseCase;
use App\Interfaces\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class FindProductController
{
    /**
     * @api {get} /api/products/:id Show product
     * @apiName ShowProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Product ID.
     */
    public function __invoke(
        int $id,
        FindProductUseCase $useCase,
    ): JsonResponse {
        $products = $useCase->execute($id);
        return ProductResource::make($products)->response();
    }
}
