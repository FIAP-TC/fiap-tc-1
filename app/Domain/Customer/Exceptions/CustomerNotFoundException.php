<?php

namespace App\Domain\Customer\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class CustomerNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Cliente com o ID {$id} não foi encontrado.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}