<?php

namespace App\Domain\Vehicule\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;


class VehiculeNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Veiculo com o ID {$id} não foi encontrado.");
    }

    public static function notFound(): self
    {
        return new self("Veículo não foi encontrado.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}