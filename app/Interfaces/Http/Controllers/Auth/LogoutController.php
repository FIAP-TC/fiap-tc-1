<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Auth\UseCases\LogoutUseCase;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

final class LogoutController
{
    /**
     * @api {post} /api/auth/logout Logout
     * @apiName Logout
     * @apiGroup Auth
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        LogoutUseCase $useCase,
    ): JsonResponse {
        try {
            $useCase->execute();
            return response()->json(['message' => 'Logout realizado com sucesso.']);
        } catch (JWTException) {
            return response()->json(['message' => 'Token não fornecido.'], 401);
        }
    }
}