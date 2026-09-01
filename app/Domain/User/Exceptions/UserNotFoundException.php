<?php

namespace App\Domain\User\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

class UserNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Usuário com o ID {$id} não foi encontrado.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'user_not_found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
