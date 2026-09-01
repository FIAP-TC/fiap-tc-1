<?php

namespace App\Domain\ServiceOrder\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class ServiceOrderNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Ordem de Serviço com o ID {$id} não foi encontrada.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'service_order_not_found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
