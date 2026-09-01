<?php

namespace App\Domain\Product\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class ProductCreationFailedException extends DomainException
{
    public static function create(): self
    {
        return new self('Não foi possível criar o produto.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'product_creation_failed',
            'message' => $this->getMessage(),
        ], 422);
    }
}