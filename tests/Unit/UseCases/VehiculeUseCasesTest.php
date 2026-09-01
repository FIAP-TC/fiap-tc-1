<?php

namespace Tests\Unit\UseCases;

use App\Application\Vehicule\DTOs\VehiculeDTO;
use App\Application\Vehicule\UseCases\CreateVehiculeUseCase;
use App\Application\Vehicule\UseCases\DeleteVehiculeUseCase;
use App\Application\Vehicule\UseCases\FindVehiculeByCustomerUseCase;
use App\Application\Vehicule\UseCases\FindVehiculeUseCase;
use App\Application\Vehicule\UseCases\ListVehiculesUseCase;
use App\Application\Vehicule\UseCases\UpdateVehiculeUseCase;
use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Domain\Vehicule\Exceptions\VehiculeNotFoundException;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use Mockery;
use Tests\TestCase;

class VehiculeUseCasesTest extends TestCase
{
    private VehiculeRepositoryInterface $vehiculeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vehiculeRepository = Mockery::mock(VehiculeRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeVehicule(string $plate = 'ABC1234', ?int $id = 1): VehiculeEntity
    {
        return new VehiculeEntity(
            id: $id,
            name: 'Meu Carro',
            plate: $plate,
            model: 'Civic',
            brand: 'Honda',
            years: 2020,
            customerId: 1,
            status: 1,
        );
    }

    public function test_list_retorna_array_de_vehicules(): void
    {
        $this->vehiculeRepository->shouldReceive('findAll')->andReturn([$this->makeVehicule()]);

        $result = (new ListVehiculesUseCase($this->vehiculeRepository))->execute();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_find_by_id_existente_retorna_vehicule(): void
    {
        $this->vehiculeRepository->shouldReceive('findById')->with(1)->andReturn($this->makeVehicule('ABC1234'));

        $result = (new FindVehiculeUseCase($this->vehiculeRepository))->execute(1);

        $this->assertEquals('ABC1234', $result->getPlate());
    }

    public function test_find_by_id_inexistente_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(VehiculeNotFoundException::class);

        (new FindVehiculeUseCase($this->vehiculeRepository))->execute(99);
    }

    public function test_find_by_customer_retorna_colecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findByCustomer')->with(1)->andReturn([$this->makeVehicule()]);

        $result = (new FindVehiculeByCustomerUseCase($this->vehiculeRepository))->execute(1);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_find_by_customer_sem_vehicules_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findByCustomer')->with(99)->andReturn([]);

        $this->expectException(VehiculeNotFoundException::class);

        (new FindVehiculeByCustomerUseCase($this->vehiculeRepository))->execute(99);
    }

    public function test_create_retorna_vehicule_com_id(): void
    {
        $this->vehiculeRepository->shouldReceive('create')->andReturn($this->makeVehicule('XYZ9999', 7));

        $dto = new VehiculeDTO(
            name: 'Novo Carro',
            plate: 'XYZ9999',
            model: 'Corolla',
            brand: 'Toyota',
            years: 2022,
            customerId: 1,
        );

        $result = (new CreateVehiculeUseCase($this->vehiculeRepository))->execute($dto);

        $this->assertEquals('XYZ9999', $result->getPlate());
        $this->assertEquals(7, $result->getId());
    }

    public function test_update_vehicule_existente(): void
    {
        $this->vehiculeRepository->shouldReceive('findById')->with(1)->andReturn($this->makeVehicule());
        $this->vehiculeRepository->shouldReceive('update')
            ->with(1, Mockery::type('array'))
            ->andReturn($this->makeVehiculeUpdated());

        $result = (new UpdateVehiculeUseCase($this->vehiculeRepository))
            ->execute(1, new VehiculeDTO(name: 'Carro Atualizado'));

        $this->assertEquals('Carro Atualizado', $result->getName());
    }

    private function makeVehiculeUpdated(): VehiculeEntity
    {
        return new VehiculeEntity(
            id: 1,
            name: 'Carro Atualizado',
            plate: 'ABC1234',
            model: 'Civic',
            brand: 'Honda',
            years: 2020,
            customerId: 1,
            status: 1,
        );
    }

    public function test_update_vehicule_inexistente_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(VehiculeNotFoundException::class);

        (new UpdateVehiculeUseCase($this->vehiculeRepository))->execute(99, new VehiculeDTO(name: 'x'));
    }

    public function test_delete_vehicule_existente(): void
    {
        $this->vehiculeRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $result = (new DeleteVehiculeUseCase($this->vehiculeRepository))->execute(1);

        $this->assertTrue($result);
    }

    public function test_delete_vehicule_inexistente_lanca_excecao(): void
    {
        $this->vehiculeRepository->shouldReceive('delete')->with(99)->andReturn(false);

        $this->expectException(VehiculeNotFoundException::class);

        (new DeleteVehiculeUseCase($this->vehiculeRepository))->execute(99);
    }
}
