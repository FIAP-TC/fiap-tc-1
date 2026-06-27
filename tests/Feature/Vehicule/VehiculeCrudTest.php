<?php

namespace Tests\Feature\Vehicule;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class VehiculeCrudTest extends TestCase
{
    use RefreshDatabase;

    private string   $adminToken;
    private string   $managerToken;
    private string   $mecanicoToken;
    private Customer $customer;
    private Vehicule $vehicule;

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

        $this->vehicule = Vehicule::create([
            'name'          => 'Meu Carro',
            'plate'         => 'ABC1234',
            'model'         => 'Civic',
            'brand'         => 'Honda',
            'years'         => 2020,
            'customer_id'   => $this->customer->id,
            'create_date'   => now(),
            'modified_date' => now(),
        ]);
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    // -------------------------------------------------------------------------
    // Listar veículos
    // -------------------------------------------------------------------------

    public function test_listar_vehicules_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/vehicules');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'plate', 'model', 'brand', 'years']]]);
    }

    public function test_listar_vehicules_sem_token_retorna_401(): void
    {
        $this->getJson('/api/vehicules')->assertStatus(401);
    }

    public function test_listar_vehicules_como_mecanico_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->mecanicoToken))
            ->getJson('/api/vehicules')
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Listar por cliente
    // -------------------------------------------------------------------------

    public function test_listar_vehicules_por_cliente(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/customers/{$this->customer->id}/vehicules");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'plate']]]);
    }

    public function test_listar_vehicules_de_cliente_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/customers/9999/vehicules')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Buscar por ID
    // -------------------------------------------------------------------------

    public function test_buscar_vehicule_existente(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/vehicules/{$this->vehicule->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['plate' => 'ABC1234']]);
    }

    public function test_buscar_vehicule_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/vehicules/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Criar veículo
    // -------------------------------------------------------------------------

    public function test_criar_vehicule_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/vehicules', [
                'name'        => 'Novo Carro',
                'plate'       => 'XYZ9999',
                'model'       => 'Corolla',
                'brand'       => 'Toyota',
                'years'       => 2022,
                'customer_id' => $this->customer->id,
            ]);

        $response->assertStatus(201)
            ->assertJson(['data' => ['plate' => 'XYZ9999']]);
    }

    public function test_criar_vehicule_com_placa_duplicada_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/vehicules', [
                'name'        => 'Duplicado',
                'plate'       => 'ABC1234', // já existe
                'model'       => 'Model',
                'brand'       => 'Brand',
                'years'       => 2020,
                'customer_id' => $this->customer->id,
            ])
            ->assertStatus(422);
    }

    public function test_criar_vehicule_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->postJson('/api/vehicules', [
                'name'        => 'Carro',
                'plate'       => 'GER0001',
                'model'       => 'Model',
                'brand'       => 'Brand',
                'years'       => 2021,
                'customer_id' => $this->customer->id,
            ])
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Atualizar veículo
    // -------------------------------------------------------------------------

    public function test_atualizar_vehicule_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson("/api/vehicules/{$this->vehicule->id}", [
                'name' => 'Carro Atualizado',
            ]);

        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'Carro Atualizado']]);
    }

    public function test_atualizar_vehicule_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->putJson('/api/vehicules/9999', ['name' => 'x'])
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Excluir veículo
    // -------------------------------------------------------------------------

    public function test_excluir_vehicule_como_admin(): void
    {
        $extra = Vehicule::create([
            'name'          => 'Para Deletar',
            'plate'         => 'DEL9999',
            'model'         => 'Model',
            'brand'         => 'Brand',
            'years'         => 2019,
            'customer_id'   => $this->customer->id,
            'create_date'   => now(),
            'modified_date' => now(),
        ]);

        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson("/api/vehicules/{$extra->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Vehicule deleted successfully.']);

        // Registro inativado — não deve mais aparecer no GET
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/vehicules/{$extra->id}")
            ->assertStatus(404);
    }

    public function test_excluir_vehicule_como_gerente_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->managerToken))
            ->deleteJson("/api/vehicules/{$this->vehicule->id}")
            ->assertStatus(403);
    }

    public function test_excluir_vehicule_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson('/api/vehicules/9999')
            ->assertStatus(404);
    }
}
