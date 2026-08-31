<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\DTOs\ProductDTO;
use App\Application\Product\UseCases\CreateProductUseCase;
use App\Application\Product\UseCases\FindProductUseCase;
use App\Interfaces\Http\Requests\Product\CreateProductRequest;
use App\Interfaces\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class CreateProductController
{
    /**
     * @api {post} /api/products Create product
     * @apiName CreateProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     *
     * @apiBody {String} name Product name.
     * @apiBody {String} type Product type.
     * @apiBody {Number} value Product value.
     * @apiBody {Number} quantity Product quantity.
     * @apiBody {Boolean} status Product status.
     */
    public function __invoke(
        CreateProductRequest $request,
        CreateProductUseCase $useCase,
    ): JsonResponse {
        $productDTO = ProductDTO::fromArray($request->validated());
        $product = $useCase->execute($productDTO);
        return ProductResource::make($product)->response();
    }
}
