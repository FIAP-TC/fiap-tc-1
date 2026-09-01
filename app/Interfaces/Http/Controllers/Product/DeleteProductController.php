<?php

namespace App\Interfaces\Http\Controllers\Product;

use App\Application\Product\UseCases\DeleteProductUseCase;
use Illuminate\Http\JsonResponse;

final class DeleteProductController
{
    /**
     * @api {delete} /api/products/:id Delete product
     * @apiName DeleteProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Product ID.
     */
    public function __invoke(
        int $id,
        DeleteProductUseCase $useCase,
    ): JsonResponse {
        $useCase->execute($id);
        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}