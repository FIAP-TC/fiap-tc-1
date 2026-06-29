<?php

namespace Tests\Unit\Services;

use App\DTOs\ServiceOrder\ServiceOrderDTO;
use App\DTOs\ServiceOrder\ServiceOrderItemsDTO;
use App\Entities\ServiceOrderEntity;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Vehicule;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderStatusRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use App\Services\ServiceOrderService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ServiceOrderServiceTest extends TestCase
{
    private ServiceOrderService                  $service;
    private ServiceOrderRepositoryInterface       $orderRepo;
    private ServiceOrderStatusRepositoryInterface $statusRepo;
    private ProductRepositoryInterface            $productRepo;
    private ServiceRepositoryInterface            $serviceRepo;
    private VehiculeRepositoryInterface           $vehiculeRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepo    = Mockery::mock(ServiceOrderRepositoryInterface::class);
        $this->statusRepo   = Mockery::mock(ServiceOrderStatusRepositoryInterface::class);
        $this->productRepo  = Mockery::mock(ProductRepositoryInterface::class);
        $this->serviceRepo  = Mockery::mock(ServiceRepositoryInterface::class);
        $this->vehiculeRepo = Mockery::mock(VehiculeRepositoryInterface::class);

        $this->service = new ServiceOrderService(
            $this->orderRepo,
            $this->statusRepo,
            $this->productRepo,
            $this->serviceRepo,
            $this->vehiculeRepo,
        );
    }

    // -------------------------------------------------------------------------
    // findAll / findById
    // -------------------------------------------------------------------------

    public function test_find_all_retorna_colecao(): void
    {
        $this->orderRepo->shouldReceive('findAll')->andReturn(new Collection());

        $result = $this->service->findAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_id_existente_retorna_order(): void
    {
        $order = new ServiceOrder();
        $order->setAttribute('order_value', 0.00);

        $this->orderRepo->shouldReceive('findById')->with(1)->andReturn($order);

        $result = $this->service->findById(1);

        $this->assertInstanceOf(ServiceOrder::class, $result);
    }

    public function test_find_by_id_inexistente_retorna_null(): void
    {
        $this->orderRepo->shouldReceive('findById')->with(99)->andReturn(null);

        $result = $this->service->findById(99);

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // create — inclui histórico de status inicial
    // -------------------------------------------------------------------------

    public function test_create_cria_ordem_e_registra_status_recebida(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setAttribute('customer_id', 7);

        $created     = new ServiceOrder();
        $created->id = 1;
        $created->setAttribute('users_id', 1);
        $created->setAttribute('users_role_id', 1);

        $fresh = new ServiceOrder();
        $fresh->setAttribute('order_value', 0.00);

        $this->vehiculeRepo
            ->shouldReceive('findByIdIgnoringStatus')
            ->with(5)
            ->andReturn($vehicule);

        DB::shouldReceive('transaction')->once()->andReturnUsing(fn($cb) => $cb());

        $this->orderRepo->shouldReceive('create')->once()->andReturn($created);

        $this->orderRepo
            ->shouldReceive('createStatusHistory')
            ->once()
            ->with(1, ServiceOrderEntity::STATUS_RECEBIDA, 7, 1, 1);

        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($fresh);

        $dto    = new ServiceOrderDTO(usersId: 1, usersRoleId: 1, vehiculesId: 5);
        $result = $this->service->create($dto);

        $this->assertInstanceOf(ServiceOrder::class, $result);
    }

    // -------------------------------------------------------------------------
    // addItems
    // -------------------------------------------------------------------------

    public function test_add_items_produtos_e_servicos_recalcula_total(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setAttribute('customer_id', 7);

        $order = new ServiceOrder();
        $order->id = 1;
        $order->setAttribute('users_id', 1);
        $order->setAttribute('users_role_id', 1);
        $order->setAttribute('vehicules_id', 5);

        $product = new Product();
        $product->id = 2;
        $product->setAttribute('value', 100.00);

        $service = new Service();
        $service->id = 3;
        $service->setAttribute('value', 80.00);

        $fresh = new ServiceOrder();
        $fresh->setAttribute('order_value', 180.00);

        DB::shouldReceive('transaction')->once()->andReturnUsing(fn($cb) => $cb());

        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($order, $fresh);
        $this->vehiculeRepo->shouldReceive('findByIdIgnoringStatus')->with(5)->andReturn($vehicule);
        $this->productRepo->shouldReceive('findManyByIds')->with([2])->andReturn(new Collection([$product]));
        $this->serviceRepo->shouldReceive('findManyByIds')->with([3])->andReturn(new Collection([$service]));
        $this->orderRepo->shouldReceive('addProducts')->once();
        $this->orderRepo->shouldReceive('addServices')->once();
        $this->orderRepo->shouldReceive('calculateOrderTotal')->with(1)->andReturn(180.00);
        $this->orderRepo->shouldReceive('updateOrderValue')->with(1, 180.00)->andReturn(true);

        $dto    = new ServiceOrderItemsDTO(productIds: [2], serviceIds: [3]);
        $result = $this->service->addItems(1, $dto);

        $this->assertEquals(180.00, $result->order_value);
    }

    public function test_add_items_produto_inexistente_lanca_excecao(): void
    {
        $order = new ServiceOrder();
        $order->id = 1;

        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($order);
        $this->productRepo->shouldReceive('findManyByIds')->with([99])->andReturn(new Collection());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(422);

        $this->service->addItems(1, new ServiceOrderItemsDTO(productIds: [99]));
    }

    public function test_add_items_servico_inexistente_lanca_excecao(): void
    {
        $order = new ServiceOrder();
        $order->id = 1;

        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($order);
        $this->productRepo->shouldReceive('findManyByIds')->with([])->andReturn(new Collection());
        $this->serviceRepo->shouldReceive('findManyByIds')->with([99])->andReturn(new Collection());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(422);

        $this->service->addItems(1, new ServiceOrderItemsDTO(serviceIds: [99]));
    }

    public function test_add_items_ordem_inexistente_lanca_excecao(): void
    {
        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->service->addItems(99, new ServiceOrderItemsDTO(productIds: [1]));
    }

    // -------------------------------------------------------------------------
    // delete
    // -------------------------------------------------------------------------

    public function test_delete_ordem_existente(): void
    {
        $order     = new ServiceOrder();
        $order->id = 1;

        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($order);
        $this->orderRepo->shouldReceive('delete')->with(1)->andReturn(true);

        $result = $this->service->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_ordem_inexistente_lanca_excecao(): void
    {
        $this->orderRepo->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->service->delete(99);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
