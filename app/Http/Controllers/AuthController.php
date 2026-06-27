<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginDTO;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Controller de autenticação.
 *
 * Responsabilidade: receber request → chamar AuthService → retornar resposta.
 * Nenhuma regra de negócio deve existir aqui.
 */
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /**
     * @api {post} /api/auth/login Login
     *
     * @apiGroup Auth
     *
     * @apiName Login
     *
     * @apiVersion 1.0.0
     *
     * @apiBody {String} username Username do usuário.
     * @apiBody {String} password Senha do usuário (mínimo 6 caracteres).
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": {
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *         "token_type": "bearer",
     *         "expires_in": 3600
     *     }
     * }
     *
     * @apiErrorExample {json} Invalid-Credentials:
     * HTTP/1.1 401 Unauthorized
     * {
     *     "success": false,
     *     "errors": ["Credenciais inválidas."],
     *     "data": []
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login(LoginDTO::fromArray($request->validated()));

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'errors'  => [$e->getMessage()],
                'data'    => [],
            ], $e->getCode() ?: 401);
        }
    }

    /**
     * @api {get} /api/auth/me Usuário autenticado
     *
     * @apiGroup Auth
     *
     * @apiName Me
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": {
     *         "id": 1,
     *         "username": "admin",
     *         "status": true,
     *         "role": { "id": 1, "name": "Administrador" }
     *     }
     * }
     */
    public function me(): JsonResponse
    {
        try {
            $user = $this->authService->me();

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => new UserResource($user),
            ]);
        } catch (TokenExpiredException) {
            return $this->tokenExpiredResponse();
        } catch (TokenInvalidException) {
            return $this->tokenInvalidResponse();
        } catch (JWTException) {
            return $this->tokenMissingResponse();
        }
    }

    /**
     * @api {post} /api/auth/logout Logout
     *
     * @apiGroup Auth
     *
     * @apiName Logout
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * { "success": true, "errors": [], "data": { "message": "Logout realizado com sucesso." } }
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => ['message' => 'Logout realizado com sucesso.'],
            ]);
        } catch (JWTException) {
            return $this->tokenMissingResponse();
        }
    }

    /**
     * @api {post} /api/auth/refresh Renovar token
     *
     * @apiGroup Auth
     *
     * @apiName RefreshToken
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token expirado ou válido.
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = $this->authService->refresh();

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
            ]);
        } catch (JWTException) {
            return $this->tokenInvalidResponse();
        }
    }

    // -------------------------------------------------------------------------
    // Helpers de resposta de erro padronizados
    // -------------------------------------------------------------------------

    private function tokenExpiredResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors'  => ['Token expirado. Faça login novamente ou utilize /auth/refresh.'],
            'data'    => [],
        ], 401);
    }

    private function tokenInvalidResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors'  => ['Token inválido.'],
            'data'    => [],
        ], 401);
    }

    private function tokenMissingResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors'  => ['Token não fornecido.'],
            'data'    => [],
        ], 401);
    }
}
