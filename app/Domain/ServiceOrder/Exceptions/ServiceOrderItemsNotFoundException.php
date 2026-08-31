<?php

namespace App\Domain\ServiceOrder\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class ServiceOrderItemsNotFoundException extends DomainException
{
    public static function forProducts(array $missingIds): self
    {
        return new self('Produtos não encontrados: ' . implode(', ', $missingIds) . '.');
    }

    public static function forServices(array $missingIds): self
    {
        return new self('Serviços não encontrados: ' . implode(', ', $missingIds) . '.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'service_order_items_not_found',
            'message' => $this->getMessage(),
        ], 422);
    }
}
