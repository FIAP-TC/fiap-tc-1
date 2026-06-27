<?php

namespace App\Http\Controllers;

use App\DTOs\Product\ProductDTO;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UpdateProductStockRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    /**
     * @api {get} /api/products List products
     * @apiName ListProducts
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ProductResource::collection(
                $this->productService->findAll()
            ),
        ]);
    }

    /**
     * @api {get} /api/products/:id Show product
     * @apiName ShowProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Product ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

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
    public function store(CreateProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $product = $this->productService->create($dto);

        return response()->json([
            'data' => new ProductResource($product),
        ], 201);
    }

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
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $dto = ProductDTO::fromArray($request->validated());
            $this->productService->update($id, $dto);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json([
            'message' => 'Product updated successfully.',
        ]);
    }

    /**
     * @api {delete} /api/products/:id Delete product
     * @apiName DeleteProduct
     * @apiGroup Product
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Product ID.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function increaseStock(UpdateProductStockRequest $request, int $id): JsonResponse
    {
        try {
            $quantityValidated = $request->validated()['quantity'];
            $this->productService->increaseStock($id, $quantityValidated);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json([
            'message' => 'Stock updated successfully.',
        ]);
    }

    public function decreaseStock(UpdateProductStockRequest $request, int $id): JsonResponse
    {
        try {
            $quantityValidated = $request->validated()['quantity'];
            $this->productService->decreaseStock($id, $quantityValidated);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }

        return response()->json([
            'message' => 'Stock updated successfully.',
        ]);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], $status);
    }
}
