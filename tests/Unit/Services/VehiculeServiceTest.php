<?php

namespace Tests\Unit\Services;

use App\DTOs\Vehicule\CreateVehiculeDTO;
use App\DTOs\Vehicule\UpdateVehiculeDTO;
use App\Models\Customer;
use App\Models\Vehicule;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use App\Services\VehiculeService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class VehiculeServiceTest extends TestCase
{
    private VehiculeService             $vehiculeService;
    private VehiculeRepositoryInterface $vehiculeRepository;
    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vehiculeRepository = Mockery::mock(VehiculeRepositoryInterface::class);
        $this->customerRepository = Mockery::mock(CustomerRepositoryInterface::class);
        $this->vehiculeService    = new VehiculeService($this->vehiculeRepository, $this->customerRepository);
    }

    public function test_find_all_retorna_colecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findAll')->andReturn(new Collection());

        $result = $this->vehiculeService->findAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_id_existente_retorna_vehicule(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setAttribute('plate', 'ABC1234');

        $this->vehiculeRepository->shouldReceive('findById')->with(1)->andReturn($vehicule);

        $result = $this->vehiculeService->findById(1);

        $this->assertEquals('ABC1234', $result->plate);
    }

    public function test_find_by_customer_retorna_colecao(): void
    {
        $customer     = new Customer();
        $customer->id = 1;

        $this->customerRepository->shouldReceive('findById')->with(1)->andReturn($customer);
        $this->vehiculeRepository->shouldReceive('findByCustomer')->with(1)->andReturn(new Collection());

        $result = $this->vehiculeService->findByCustomer(1);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->vehiculeService->findByCustomer(99);
    }

    public function test_create_retorna_vehicule_com_id(): void
    {
        $customer     = new Customer();
        $customer->id = 1;

        $created     = new Vehicule();
        $created->id = 7;

        $fresh = new Vehicule();
        $fresh->setAttribute('plate', 'XYZ9999');

        $this->customerRepository->shouldReceive('findById')->with(1)->andReturn($customer);
        $this->vehiculeRepository->shouldReceive('create')->andReturn($created);
        $this->vehiculeRepository->shouldReceive('findById')->with(7)->andReturn($fresh);

        $dto    = new CreateVehiculeDTO('Novo Carro', 'XYZ9999', 'Corolla', 'Toyota', 2022, 1);
        $result = $this->vehiculeService->create($dto);

        $this->assertEquals('XYZ9999', $result->plate);
    }

    public function test_create_com_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->vehiculeService->create(new CreateVehiculeDTO('Car', 'PLT0001', 'Model', 'Brand', 2020, 99));
    }

    public function test_update_vehicule_existente(): void
    {
        $vehicule     = new Vehicule();
        $vehicule->id = 1;

        $fresh = new Vehicule();
        $fresh->setAttribute('name', 'Carro Atualizado');

        $this->vehiculeRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($vehicule, $fresh);
        $this->vehiculeRepository->shouldReceive('update')->with(1, Mockery::type('array'))->andReturn(true);

        $dto    = new UpdateVehiculeDTO(name: 'Carro Atualizado');
        $result = $this->vehiculeService->update(1, $dto);

        $this->assertEquals('Carro Atualizado', $result->name);
    }

    public function test_update_vehicule_inexistente_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->vehiculeService->update(99, new UpdateVehiculeDTO(name: 'x'));
    }

    public function test_delete_vehicule_existente(): void
    {
        $vehicule     = new Vehicule();
        $vehicule->id = 1;

        $this->vehiculeRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($vehicule);
        $this->vehiculeRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $this->vehiculeService->delete(1);
        $this->assertTrue(true);
    }

    public function test_delete_vehicule_inexistente_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->vehiculeService->delete(99);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
