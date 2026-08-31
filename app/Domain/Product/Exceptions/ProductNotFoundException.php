<?php

namespace App\Domain\Product\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class ProductNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Produto com o ID {$id} não foi encontrado.");
    }

    public static function notFound(): self
    {
        return new self("Produto não foi encontrado.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'product_not_found',
            'message' => $this->getMessage(),
        ], 404);
    }
}