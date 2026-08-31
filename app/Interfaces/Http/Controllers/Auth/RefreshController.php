<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Auth\UseCases\RefreshUseCase;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

final class RefreshController
{
    /**
     * @api {post} /api/auth/refresh Renovar token
     * @apiName RefreshToken
     * @apiGroup Auth
     * @apiHeader {String} Authorization Bearer token expirado ou válido.
     */
    public function __invoke(
        RefreshUseCase $useCase,
    ): JsonResponse {
        try {
            $token = $useCase->execute();

            return response()->json([
                'data' => [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
            ]);
        } catch (JWTException) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }
    }
}