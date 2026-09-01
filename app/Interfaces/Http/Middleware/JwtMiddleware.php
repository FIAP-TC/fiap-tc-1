<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Middleware de autenticação JWT.
 *
 * Valida o token Bearer em cada request protegido.
 * Distingue os casos de erro para retornar mensagens claras ao client:
 * - Token ausente → 401 Token não fornecido
 * - Token expirado → 401 Token expirado (client deve chamar /refresh)
 * - Token inválido → 401 Token inválido (client deve fazer login novamente)
 *
 * Este middleware substitui o 'auth:sanctum' para autenticação stateless via JWT.
 */
class JwtMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException) {
            return response()->json([
                'success' => false,
                'errors'  => ['Token expirado. Utilize /api/auth/refresh para renovar.'],
                'data'    => [],
            ], 401);
        } catch (TokenInvalidException) {
            return response()->json([
                'success' => false,
                'errors'  => ['Token inválido.'],
                'data'    => [],
            ], 401);
        } catch (JWTException) {
            return response()->json([
                'success' => false,
                'errors'  => ['Token não fornecido.'],
                'data'    => [],
            ], 401);
        }

        return $next($request);
    }
}
