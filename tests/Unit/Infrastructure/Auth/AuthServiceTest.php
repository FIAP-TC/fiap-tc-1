<?php

namespace Tests\Unit\Infrastructure\Auth;

use App\Application\Auth\DTOs\LoginDTO;
use App\Infrastructure\Auth\AuthService;
use App\Infrastructure\Persistence\Eloquent\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Testes unitários do AuthService.
 *
 * As facades são mockadas para testar apenas a lógica do serviço,
 * sem dependência de banco ou HTTP.
 */
class AuthServiceTest extends TestCase
{
    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('attempt')->with(['username' => 'admin', 'password' => 'admin123'])
            ->andReturn('fake.jwt.token');

        $dto = new LoginDTO('admin', 'admin123');
        $result = $this->authService->login($dto);

        $this->assertEquals('fake.jwt.token', $result);
    }

    public function test_login_com_credenciais_invalidas_lanca_excecao(): void
    {
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('attempt')->andReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Credenciais inválidas.');

        $this->authService->login(new LoginDTO('admin', 'errada'));
    }

    public function test_me_retorna_usuario_autenticado(): void
    {
        $user = new User(['username' => 'admin']);

        JWTAuth::shouldReceive('parseToken')->andReturnSelf();
        JWTAuth::shouldReceive('authenticate')->andReturn($user);

        $result = $this->authService->me();

        $this->assertEquals('admin', $result->username);
    }

    public function test_logout_invalida_token(): void
    {
        JWTAuth::shouldReceive('parseToken')->andReturnSelf();
        JWTAuth::shouldReceive('invalidate')->once();

        // Não deve lançar exceção
        $this->authService->logout();

        $this->assertTrue(true);
    }

    public function test_refresh_retorna_novo_token(): void
    {
        JWTAuth::shouldReceive('parseToken')->andReturnSelf();
        JWTAuth::shouldReceive('refresh')->andReturn('novo.jwt.token');

        $result = $this->authService->refresh();

        $this->assertEquals('novo.jwt.token', $result);
    }
}
