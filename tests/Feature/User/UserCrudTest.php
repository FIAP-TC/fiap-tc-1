<?php

namespace Tests\Feature\User;

use App\Infrastructure\Persistence\Eloquent\User\Models\Role;
use App\Infrastructure\Persistence\Eloquent\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;
    private string $managerToken;
    private string $mecanicoToken;
    private User   $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria os perfis com IDs fixos (correspondentes ao RoleEntity)
        Role::insert([
            ['id' => 1, 'name' => 'Administrador', 'status' => 'ativo'],
            ['id' => 2, 'name' => 'Gerente',        'status' => 'ativo'],
            ['id' => 3, 'name' => 'Mecânico',       'status' => 'ativo'],
        ]);

        $this->admin   = User::create(['username' => 'admin',   'password' => Hash::make('pass'), 'role_id' => 1, 'status' => 1, 'create_date' => now()]);
        $manager       = User::create(['username' => 'gerente', 'password' => Hash::make('pass'), 'role_id' => 2, 'status' => 1, 'create_date' => now()]);
        $mecanico      = User::create(['username' => 'mec',     'password' => Hash::make('pass'), 'role_id' => 3, 'status' => 1, 'create_date' => now()]);

        $this->adminToken   = JWTAuth::fromUser($this->admin);
        $this->managerToken = JWTAuth::fromUser($manager);
        $this->mecanicoToken = JWTAuth::fromUser($mecanico);
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    // -------------------------------------------------------------------------
    // Listar usuários
    // -------------------------------------------------------------------------

    public function test_listar_usuarios_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'username', 'status', 'role']]]);
    }

    public function test_listar_usuarios_sem_token_retorna_401(): void
    {
        $this->getJson('/api/users')->assertStatus(401);
    }

    public function test_listar_usuarios_como_mecanico_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->mecanicoToken))
            ->getJson('/api/users')
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Buscar por ID
    // -------------------------------------------------------------------------

    public function test_buscar_usuario_existente(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/users/{$this->admin->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['username' => 'admin']]);
    }

    public function test_buscar_usuario_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/users/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Criar usuário
    // -------------------------------------------------------------------------

    public function test_criar_usuario_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/users', [
                'username' => 'novo_usuario',
                'password' => 'senha123',
                'role_id'  => 3,
            ]);

        $response->assertStatus(201)
            ->assertJson(['data' => ['username' => 'novo_usuario']]);

        // Senha nunca deve aparecer na resposta
        $this->assertArrayNotHasKey('password', $response->json('data'));
    }

    public function test_criar_usuario_com_username_duplicado_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/users', [
                'username' => 'admin', // já existe
                'password' => 'senha123',
                'role_id'  => 1,
            ])
            ->assertStatus(422);
    }

    public function test_criar_usuario_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->postJson('/api/users', [
                'username' => 'outro',
                'password' => 'senha123',
                'role_id'  => 3,
            ])
            ->assertStatus(403);
    }

    public function test_criar_usuario_com_dados_invalidos_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/users', [
                'username' => '',
                'password' => '123', // muito curta
            ])
            ->assertStatus(422);
    }

    // -------------------------------------------------------------------------
    // Atualizar usuário
    // -------------------------------------------------------------------------

    public function test_atualizar_usuario_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson("/api/users/{$this->admin->id}", [
                'username' => 'admin_updated',
            ]);

        $response->assertStatus(200)
            ->assertJson(['data' => ['username' => 'admin_updated']]);
    }

    public function test_atualizar_usuario_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson('/api/users/9999', ['username' => 'x'])
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Excluir usuário
    // -------------------------------------------------------------------------

    public function test_excluir_usuario_como_admin(): void
    {
        $user = User::create(['username' => 'para_deletar', 'password' => Hash::make('pass'), 'role_id' => 3, 'status' => 1, 'create_date' => now()]);

        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Usuário removido com sucesso.']);
    }

    public function test_excluir_usuario_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->deleteJson("/api/users/{$this->admin->id}")
            ->assertStatus(403);
    }

    public function test_excluir_usuario_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson('/api/users/9999')
            ->assertStatus(404);
    }
}
