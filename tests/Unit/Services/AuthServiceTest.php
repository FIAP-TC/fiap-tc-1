<?php

namespace Tests\Unit\Services;

use App\DTOs\Auth\LoginDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Testes unitários do AuthService.
 *
 * Os repositórios e facades são mockados para testar apenas a lógica
 * do Service, sem dependência de banco ou HTTP.
 */
class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->authService    = new AuthService($this->userRepository);
    }

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('attempt')->with(['username' => 'admin', 'password' => 'admin123'])
            ->andReturn('fake.jwt.token');

        $dto    = new LoginDTO('admin', 'admin123');
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
