<?php

namespace App\Http\Middleware;

use App\Entities\RoleEntity;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Middleware de autorização para Administradores.
 *
 * Lê o role_id do payload JWT (custom claim definido em User::getJWTCustomClaims)
 * sem fazer query extra ao banco, mantendo a verificação stateless.
 *
 * Deve ser aplicado APÓS o JwtMiddleware (que garante que o token é válido).
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $payload = JWTAuth::parseToken()->getPayload();
        $roleId  = (int) $payload->get('role_id');

        if ($roleId !== RoleEntity::ID_ADMINISTRADOR) {
            return response()->json([
                'success' => false,
                'errors'  => ['Acesso restrito a Administradores.'],
                'data'    => [],
            ], 403);
        }

        return $next($request);
    }
}
