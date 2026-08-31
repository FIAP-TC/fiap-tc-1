<?php

namespace App\Domain\Product\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class InsufficientStockException extends DomainException
{
    public static function forProduct(?int $id, int $requested, int $available): self
    {
        return new self(
            "Estoque insuficiente para o produto com o ID {$id}. Solicitado: {$requested}, disponível: {$available}."
        );
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'insufficient_stock',
            'message' => $this->getMessage(),
        ], 422);
    }
}