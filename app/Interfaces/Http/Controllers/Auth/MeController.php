<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Auth\UseCases\MeUseCase;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

final class MeController
{
    /**
     * @api {get} /api/auth/me Usuário autenticado
     * @apiName Me
     * @apiGroup Auth
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        MeUseCase $useCase,
    ): JsonResponse {
        try {
            $user = $useCase->execute();
            return UserResource::make($user)->response();
        } catch (TokenExpiredException) {
            return response()->json(['message' => 'Token expirado. Faça login novamente ou utilize /auth/refresh.'], 401);
        } catch (TokenInvalidException) {
            return response()->json(['message' => 'Token inválido.'], 401);
        } catch (JWTException) {
            return response()->json(['message' => 'Token não fornecido.'], 401);
        }
    }
}