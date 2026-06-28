<?php

namespace Tests\Feature\Service;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceCrudTest extends TestCase
{
    use RefreshDatabase;

    private string  $adminToken;
    private string  $managerToken;
    private string  $mecanicoToken;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Role::insert([
            ['id' => 1, 'name' => 'Administrador', 'status' => 'ativo'],
            ['id' => 2, 'name' => 'Gerente',        'status' => 'ativo'],
            ['id' => 3, 'name' => 'Mecânico',       'status' => 'ativo'],
        ]);

        $admin    = User::create(['username' => 'admin',   'password' => Hash::make('pass'), 'role_id' => 1, 'status' => 1, 'create_date' => now()]);
        $manager  = User::create(['username' => 'gerente', 'password' => Hash::make('pass'), 'role_id' => 2, 'status' => 1, 'create_date' => now()]);
        $mecanico = User::create(['username' => 'mec',     'password' => Hash::make('pass'), 'role_id' => 3, 'status' => 1, 'create_date' => now()]);

        $this->adminToken    = JWTAuth::fromUser($admin);
        $this->managerToken  = JWTAuth::fromUser($manager);
        $this->mecanicoToken = JWTAuth::fromUser($mecanico);

        $this->service = Service::create([
            'name'          => 'Troca de óleo',
            'value'         => 120.00,
            'status'        => true,
            'create_date'   => now(),
            'modified_date' => now(),
        ]);
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    // -------------------------------------------------------------------------
    // Listar serviços
    // -------------------------------------------------------------------------

    public function test_listar_services_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'value', 'status']]]);
    }

    public function test_listar_services_como_gerente(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->getJson('/api/services')
            ->assertStatus(200);
    }

    public function test_listar_services_sem_token_retorna_401(): void
    {
        $this->getJson('/api/services')->assertStatus(401);
    }

    public function test_listar_services_como_mecanico_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->mecanicoToken))
            ->getJson('/api/services')
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Buscar por ID
    // -------------------------------------------------------------------------

    public function test_buscar_service_existente(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/services/{$this->service->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'name'  => 'Troca de óleo',
                'value' => 120.00,
            ]]);
    }

    public function test_buscar_service_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/services/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Criar serviço
    // -------------------------------------------------------------------------

    public function test_criar_service_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/services', [
                'name'  => 'Alinhamento',
                'value' => 80.00,
            ]);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'name'   => 'Alinhamento',
                'value'  => 80.00,
                'status' => true,
            ]]);
    }

    public function test_criar_service_sem_nome_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/services', ['value' => 50.00])
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_criar_service_sem_valor_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/services', ['name' => 'Balanceamento'])
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['value']]);
    }

    public function test_criar_service_com_valor_zero_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/services', ['name' => 'Serviço Grátis', 'value' => 0])
            ->assertStatus(422);
    }

    public function test_criar_service_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->postJson('/api/services', ['name' => 'Alinhamento', 'value' => 80.00])
            ->assertStatus(403);
    }

    public function test_criar_service_sem_token_retorna_401(): void
    {
        $this->postJson('/api/services', ['name' => 'Alinhamento', 'value' => 80.00])
            ->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // Atualizar serviço
    // -------------------------------------------------------------------------

    public function test_atualizar_service_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson("/api/services/{$this->service->id}", [
                'name'  => 'Troca de óleo sintético',
                'value' => 180.00,
            ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'name'  => 'Troca de óleo sintético',
                'value' => 180.00,
            ]]);
    }

    public function test_atualizar_service_como_gerente(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->putJson("/api/services/{$this->service->id}", ['name' => 'Novo Nome'])
            ->assertStatus(200);
    }

    public function test_atualizar_service_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson('/api/services/9999', ['name' => 'X'])
            ->assertStatus(404);
    }

    public function test_reativar_service_inativo(): void
    {
        // Inativa o serviço
        $this->service->update(['status' => false]);

        // Reativa via PUT com status=true
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson("/api/services/{$this->service->id}", ['status' => true]);

        $response->assertStatus(200)
            ->assertJson(['data' => ['status' => true]]);
    }

    // -------------------------------------------------------------------------
    // Excluir serviço
    // -------------------------------------------------------------------------

    public function test_excluir_service_como_admin(): void
    {
        $extra = Service::create([
            'name'        => 'Para Deletar',
            'value'       => 50.00,
            'status'      => true,
            'create_date' => now(),
        ]);

        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson("/api/services/{$extra->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Serviço excluído com sucesso.']);

        // Soft-deleted — não deve aparecer no GET
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/services/{$extra->id}")
            ->assertStatus(404);
    }

    public function test_excluir_service_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->deleteJson("/api/services/{$this->service->id}")
            ->assertStatus(403);
    }

    public function test_excluir_service_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson('/api/services/9999')
            ->assertStatus(404);
    }
}
