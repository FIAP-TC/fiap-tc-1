<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    private string   $adminToken;
    private string   $managerToken;
    private string   $mecanicoToken;
    private Customer $customer;

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

        $this->customer = Customer::create([
            'name'                  => 'João Silva',
            'identification'        => 'CPF',
            'identification_number' => 12345678901,
            'email'                 => 'joao@example.com',
            'status'                => true,
            'create_date'           => now(),
            'modified_date'         => now(),
        ]);
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    // -------------------------------------------------------------------------
    // Listar clientes
    // -------------------------------------------------------------------------

    public function test_listar_clientes_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'email', 'identification', 'status']]]);
    }

    public function test_listar_clientes_sem_token_retorna_401(): void
    {
        $this->getJson('/api/customers')->assertStatus(401);
    }

    public function test_listar_clientes_como_mecanico_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->mecanicoToken))
            ->getJson('/api/customers')
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Buscar por ID
    // -------------------------------------------------------------------------

    public function test_buscar_cliente_existente(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/customers/{$this->customer->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'João Silva']]);
    }

    public function test_buscar_cliente_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/customers/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Criar cliente
    // -------------------------------------------------------------------------

    public function test_criar_cliente_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/customers', [
                'name'                  => 'Maria Souza',
                'identification'        => 'CPF',
                'identification_number' => 98765432100,
                'email'                 => 'maria@example.com',
            ]);

        $response->assertStatus(201)
            ->assertJson(['data' => ['name' => 'Maria Souza']]);
    }

    public function test_criar_cliente_com_email_duplicado_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/customers', [
                'name'                  => 'Outro',
                'identification'        => 'CPF',
                'identification_number' => 11111111111,
                'email'                 => 'joao@example.com', // duplicado
            ])
            ->assertStatus(422);
    }

    public function test_criar_cliente_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->postJson('/api/customers', [
                'name'                  => 'Novo',
                'identification'        => 'CPF',
                'identification_number' => 22222222222,
                'email'                 => 'novo@example.com',
            ])
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Atualizar cliente
    // -------------------------------------------------------------------------

    public function test_atualizar_cliente_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson("/api/customers/{$this->customer->id}", [
                'name' => 'João Atualizado',
            ]);

        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'João Atualizado']]);
    }

    public function test_atualizar_cliente_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson('/api/customers/9999', ['name' => 'x'])
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Excluir cliente
    // -------------------------------------------------------------------------

    public function test_excluir_cliente_como_admin(): void
    {
        $extra = Customer::create([
            'name'                  => 'Para Deletar',
            'identification'        => 'CPF',
            'identification_number' => 55555555555,
            'email'                 => 'del@example.com',
            'status'                => true,
            'create_date'           => now(),
            'modified_date'         => now(),
        ]);

        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson("/api/customers/{$extra->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Customer deleted successfully.']);

        // Registro inativado — não deve mais aparecer no GET
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/customers/{$extra->id}")
            ->assertStatus(404);
    }

    public function test_excluir_cliente_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->deleteJson("/api/customers/{$this->customer->id}")
            ->assertStatus(403);
    }

    public function test_excluir_cliente_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson('/api/customers/9999')
            ->assertStatus(404);
    }
}
