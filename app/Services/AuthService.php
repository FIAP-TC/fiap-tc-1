<?php

namespace App\Services;

use App\DTOs\Auth\LoginDTO;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Serviço de autenticação.
 *
 * Fluxo JWT implementado:
 * 1. Client envia username + password para POST /api/auth/login
 * 2. AuthService tenta autenticar via guard 'api' (jwt driver)
 * 3. JWT gera um token assinado com a chave secreta (JWT_SECRET no .env)
 * 4. Client usa o token no header: Authorization: Bearer <token>
 * 5. JwtMiddleware valida o token em cada requisição protegida
 * 6. POST /api/auth/logout invalida o token (blacklist)
 *
 * O token carrega no payload: sub (user id) + role_id (custom claim).
 * O middleware de role lê role_id do token sem query extra ao banco.
 */
class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * Realiza o login e retorna o token JWT gerado.
     *
     * @throws \RuntimeException quando as credenciais são inválidas
     * @throws JWTException quando não é possível gerar o token
     */
    public function login(LoginDTO $dto): string
    {
        $credentials = [
            'username' => $dto->username,
            'password' => $dto->password,
        ];

        // Auth::guard('api')->attempt() verifica as credenciais e,
        // se válidas, retorna o token JWT gerado pelo tymon/jwt-auth
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            throw new \RuntimeException('Credenciais inválidas.', 401);
        }

        return $token;
    }

    /**
     * Retorna o usuário autenticado a partir do token JWT no request atual.
     *
     * @throws TokenExpiredException token expirado
     * @throws TokenInvalidException token com assinatura inválida
     * @throws JWTException token ausente ou malformado
     */
    public function me(): \App\Models\User
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * Invalida o token atual, adicionando-o à blacklist do JWT.
     * Requer JWT_BLACKLIST_ENABLED=true no .env.
     */
    public function logout(): void
    {
        JWTAuth::parseToken()->invalidate();
    }

    /**
     * Renova o token atual, gerando um novo com TTL resetado.
     * Útil para manter sessões longas sem pedir re-login.
     */
    public function refresh(): string
    {
        return JWTAuth::parseToken()->refresh();
    }
}
