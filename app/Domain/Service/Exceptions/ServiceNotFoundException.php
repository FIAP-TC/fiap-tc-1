<?php

namespace App\Domain\Service\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class ServiceNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Serviço com o ID {$id} não foi encontrado.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'service_not_found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
