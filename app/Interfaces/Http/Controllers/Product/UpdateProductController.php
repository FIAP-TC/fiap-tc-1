<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\DTOs\ProductDTO;
use App\Application\Product\UseCases\ListProductUseCase;
use App\Application\Product\UseCases\UpdateProductUseCase;
use App\Interfaces\Http\Requests\Product\UpdateProductRequest;
use App\Interfaces\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

final class UpdateProductController
{
    /**
     * @api {put} /api/products/:id Update product
     * @apiName UpdateProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Product ID.
     *
     * @apiBody {String} name Product name.
     * @apiBody {String} type Product type.
     * @apiBody {Number} value Product value.
     * @apiBody {Number} quantity Product quantity.
     * @apiBody {Boolean} status Product status.
     */
    public function __invoke(
        UpdateProductRequest $request,
        int $id,
        UpdateProductUseCase $useCase, 
    ): JsonResponse
    {
        $productDTO = ProductDTO::fromArray($request->validated());
        $product = $useCase->execute($id, $productDTO);
        return ProductResource::make($product)->response();
    }
}