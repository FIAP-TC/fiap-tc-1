<?php

namespace App\Http\Middleware;

use App\Entities\RoleEntity;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Middleware de autorização para Gerentes e Administradores.
 *
 * Administradores também passam por este middleware pois têm
 * permissões de gerente (hierarquia: Admin > Gerente > Mecânico).
 *
 * Lê role_id do JWT payload — sem query ao banco.
 */
class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $payload = JWTAuth::parseToken()->getPayload();
        $roleId  = (int) $payload->get('role_id');

        $allowedRoles = [RoleEntity::ID_ADMINISTRADOR, RoleEntity::ID_GERENTE];

        if (!in_array($roleId, $allowedRoles, true)) {
            return response()->json([
                'success' => false,
                'errors'  => ['Acesso restrito a Gerentes e Administradores.'],
                'data'    => [],
            ], 403);
        }

        return $next($request);
    }
}
