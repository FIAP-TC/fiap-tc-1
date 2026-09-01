<?php

namespace App\Domain\Auth\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class InvalidCredentialsException extends DomainException
{
    public static function make(): self
    {
        return new self('Credenciais inválidas.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'invalid_credentials',
            'message' => $this->getMessage(),
        ], 401);
    }
}