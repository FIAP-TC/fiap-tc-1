<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Não usar id fixo: o auto_increment do MySQL não reseta após rollback de transação,
        // então a partir do 2º teste o id seria 2, quebrando o role_id=1 hardcoded.
        $this->role = Role::create([
            'name'   => 'Administrador',
            'status' => 'ativo',
        ]);

        $this->user = User::create([
            'username'    => 'admin',
            'password'    => Hash::make('admin123'),
            'role_id'     => $this->role->id,
            'status'      => 1,
            'create_date' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'token_type', 'expires_in'],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_login_com_credenciais_invalidas_retorna_401(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'admin',
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_sem_username_retorna_422(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'admin123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }

    // -------------------------------------------------------------------------
    // Me (usuário autenticado)
    // -------------------------------------------------------------------------

    public function test_me_com_token_valido_retorna_usuario(): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => ['username' => 'admin'],
            ]);
    }

    public function test_me_sem_token_retorna_401(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_me_com_token_invalido_retorna_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer token.invalido.aqui')
            ->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function test_logout_com_token_valido_retorna_sucesso(): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_logout_sem_token_retorna_401(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
