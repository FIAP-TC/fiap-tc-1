<?php

namespace Tests\Feature\ServiceOrder;

use App\Infrastructure\Persistence\Eloquent\Customer\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Product\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrder;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;
use App\Infrastructure\Persistence\Eloquent\User\Models\Role;
use App\Infrastructure\Persistence\Eloquent\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceOrderCrudTest extends TestCase
{
    use RefreshDatabase;

    private string  $adminToken;
    private string  $managerToken;
    private string  $mecanicoToken;
    private User    $admin;
    private Vehicule $vehicule;
    private Product  $product;
    private Service  $mechService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::insert([
            ['id' => 1, 'name' => 'Administrador', 'status' => 'ativo'],
            ['id' => 2, 'name' => 'Gerente',        'status' => 'ativo'],
            ['id' => 3, 'name' => 'Mecânico',       'status' => 'ativo'],
        ]);

        // Seed dos status de ordem
        DB::table('service_order_status')->insert([
            ['id' => 1, 'name' => 'Recebida',             'status' => 'ativo', 'create_date' => now()],
            ['id' => 2, 'name' => 'Em diagnóstico',       'status' => 'ativo', 'create_date' => now()],
            ['id' => 3, 'name' => 'Aguardando aprovação', 'status' => 'ativo', 'create_date' => now()],
        ]);

        $this->admin = User::create([
            'username'    => 'admin',
            'password'    => Hash::make('pass'),
            'role_id'     => 1,
            'status'      => 1,
            'create_date' => now(),
        ]);
        $manager  = User::create(['username' => 'gerente', 'password' => Hash::make('pass'), 'role_id' => 2, 'status' => 1, 'create_date' => now()]);
        $mecanico = User::create(['username' => 'mec',     'password' => Hash::make('pass'), 'role_id' => 3, 'status' => 1, 'create_date' => now()]);

        $this->adminToken    = JWTAuth::fromUser($this->admin);
        $this->managerToken  = JWTAuth::fromUser($manager);
        $this->mecanicoToken = JWTAuth::fromUser($mecanico);

        $customer = Customer::create([
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
            'status'        => true,
            'customer_id'   => $customer->id,
            'create_date'   => now(),
            'modified_date' => now(),
        ]);

        $this->product = Product::create([
            'name'          => 'Filtro de óleo',
            'type'          => 'PECAS',
            'value'         => 45.00,
            'quantity'      => 10,
            'status'        => true,
            'create_date'   => now(),
            'modified_date' => now(),
        ]);

        $this->mechService = Service::create([
            'name'        => 'Troca de óleo',
            'value'       => 80.00,
            'status'      => true,
            'create_date' => now(),
        ]);
    }

    private function authHeader(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    // -------------------------------------------------------------------------
    // Criar Ordem de Serviço
    // -------------------------------------------------------------------------

    public function test_criar_ordem_como_admin(): void
    {
        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/service-orders', [
                'vehicules_id' => $this->vehicule->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'order_value', 'status', 'status_history']]);

        // Confirma que o status inicial "Recebida" foi registrado
        $this->assertDatabaseHas('service_order_has_service_order_status', [
            'service_order_status_id' => 1,
        ]);
    }

    public function test_criar_ordem_sem_veiculo_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/service-orders', [])
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['vehicules_id']]);
    }

    public function test_criar_ordem_com_veiculo_inexistente_retorna_422(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/service-orders', ['vehicules_id' => 9999])
            ->assertStatus(422);
    }

    public function test_criar_ordem_sem_token_retorna_401(): void
    {
        $this->postJson('/api/service-orders', ['vehicules_id' => $this->vehicule->id])
            ->assertStatus(401);
    }

    public function test_criar_ordem_como_mecanico_retorna_403(): void
    {
        $this->withHeaders($this->authHeader($this->mecanicoToken))
            ->postJson('/api/service-orders', ['vehicules_id' => $this->vehicule->id])
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Listar Ordens
    // -------------------------------------------------------------------------

    public function test_listar_ordens_como_admin(): void
    {
        $this->criarOrdem();

        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/service-orders');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'order_value', 'status']]]);
    }

    public function test_listar_ordens_sem_token_retorna_401(): void
    {
        $this->getJson('/api/service-orders')->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // Buscar por ID
    // -------------------------------------------------------------------------

    public function test_buscar_ordem_existente(): void
    {
        $order = $this->criarOrdem();

        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/service-orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'order_value', 'products', 'services', 'status_history']]);
    }

    public function test_buscar_ordem_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson('/api/service-orders/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Soft Delete
    // -------------------------------------------------------------------------

    public function test_excluir_ordem_como_admin(): void
    {
        $order = $this->criarOrdem();

        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson("/api/service-orders/{$order->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Ordem de Serviço excluída com sucesso.']);

        // Soft-delete — não deve aparecer no GET
        $this->withHeaders($this->authHeader($this->adminToken))
            ->getJson("/api/service-orders/{$order->id}")
            ->assertStatus(404);

        // Registro ainda existe no banco com status=false
        $this->assertDatabaseHas('service_order', ['id' => $order->id, 'status' => false]);
    }

    public function test_excluir_ordem_como_gerente_retorna_403(): void
    {
        $order = $this->criarOrdem();

        $this->withHeaders($this->authHeader($this->managerToken))
            ->deleteJson("/api/service-orders/{$order->id}")
            ->assertStatus(403);
    }

    public function test_excluir_ordem_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->deleteJson('/api/service-orders/9999')
            ->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Adicionar Itens
    // -------------------------------------------------------------------------

    public function test_adicionar_produto_a_ordem(): void
    {
        $order = $this->criarOrdem();

        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [
                'products' => [$this->product->id],
            ]);

        $response->assertStatus(200);
        $this->assertEquals(45.00, $response->json('data.order_value'));

        $this->assertDatabaseHas('service_order_has_products', [
            'service_order_id' => $order->id,
            'products_id'      => $this->product->id,
        ]);
    }

    public function test_adicionar_servico_a_ordem(): void
    {
        $order = $this->criarOrdem();

        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [
                'services' => [$this->mechService->id],
            ]);

        $response->assertStatus(200);
        $this->assertEquals(80.00, $response->json('data.order_value'));

        $this->assertDatabaseHas('service_order_has_services', [
            'service_order_id' => $order->id,
            'services_id'      => $this->mechService->id,
        ]);
    }

    public function test_adicionar_produto_e_servico_juntos_recalcula_total(): void
    {
        $order = $this->criarOrdem();

        $response = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [
                'products' => [$this->product->id],
                'services' => [$this->mechService->id],
            ]);

        $response->assertStatus(200);
        $this->assertEquals(125.00, $response->json('data.order_value')); // 45 + 80
    }

    public function test_adicionar_itens_em_ordem_inexistente_retorna_404(): void
    {
        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson('/api/service-orders/9999/items', [
                'products' => [$this->product->id],
            ])
            ->assertStatus(404);
    }

    public function test_adicionar_produto_inexistente_retorna_422(): void
    {
        $order = $this->criarOrdem();

        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [
                'products' => [9999],
            ])
            ->assertStatus(422);
    }

    public function test_adicionar_servico_inexistente_retorna_422(): void
    {
        $order = $this->criarOrdem();

        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [
                'services' => [9999],
            ])
            ->assertStatus(422);
    }

    public function test_adicionar_itens_sem_produtos_nem_servicos_retorna_422(): void
    {
        $order = $this->criarOrdem();

        $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", [])
            ->assertStatus(422);
    }

    public function test_valor_acumulado_em_chamadas_successivas(): void
    {
        $order = $this->criarOrdem();

        // Primeira chamada: adiciona produto (45.00)
        $r1 = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", ['products' => [$this->product->id]]);
        $this->assertEquals(45.00, $r1->json('data.order_value'));

        // Segunda chamada: adiciona serviço (80.00) → total acumulado = 125.00
        $r2 = $this->withHeaders($this->authHeader($this->adminToken))
            ->postJson("/api/service-orders/{$order->id}/items", ['services' => [$this->mechService->id]]);
        $this->assertEquals(125.00, $r2->json('data.order_value'));
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function criarOrdem(): ServiceOrder
    {
        $order = ServiceOrder::create([
            'users_id'      => $this->admin->id,
            'users_role_id' => 1,
            'vehicules_id'  => $this->vehicule->id,
            'order_value'   => 0.00,
            'status'        => true,
            'create_date'   => now(),
        ]);

        DB::table('service_order_has_service_order_status')->insert([
            'service_order_id'            => $order->id,
            'service_order_customer_id'   => 0,
            'service_order_users_id'      => $order->users_id,
            'service_order_users_role_id' => $order->users_role_id,
            'service_order_status_id'     => 1,
            'create_date'                 => now(),
        ]);

        return $order;
    }
}
