<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Auth\DTOs\LoginDTO;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Interfaces\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

final class LoginController
{
    /**
     * @api {post} /api/auth/login Login
     * @apiName Login
     * @apiGroup Auth
     * @apiBody {String} username Username do usuário.
     * @apiBody {String} password Senha do usuário (mínimo 6 caracteres).
     */
    public function __invoke(
        LoginRequest $request,
        LoginUseCase $useCase,
    ): JsonResponse {
        $token = $useCase->execute(LoginDTO::fromArray($request->validated()));

        return response()->json([
            'data' => [
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ]);
    }
}