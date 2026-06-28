<?php

namespace Tests\Unit\Services;

use App\DTOs\Service\ServiceDTO;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ServiceServiceTest extends TestCase
{
    private ServiceService             $serviceService;
    private ServiceRepositoryInterface $serviceRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceRepository = Mockery::mock(ServiceRepositoryInterface::class);
        $this->serviceService    = new ServiceService($this->serviceRepository);
    }

    public function test_find_all_retorna_colecao(): void
    {
        $this->serviceRepository->shouldReceive('findAll')->andReturn(new Collection());

        $result = $this->serviceService->findAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_id_existente_retorna_service(): void
    {
        $service = new Service();
        $service->setAttribute('name', 'Troca de óleo');

        $this->serviceRepository->shouldReceive('findById')->with(1)->andReturn($service);

        $result = $this->serviceService->findById(1);

        $this->assertEquals('Troca de óleo', $result->name);
    }

    public function test_find_by_id_inexistente_retorna_null(): void
    {
        $this->serviceRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $result = $this->serviceService->findById(99);

        $this->assertNull($result);
    }

    public function test_create_retorna_service_com_id(): void
    {
        $created     = new Service();
        $created->id = 5;

        $fresh = new Service();
        $fresh->setAttribute('name', 'Alinhamento');
        $fresh->setAttribute('value', 80.00);

        $this->serviceRepository->shouldReceive('create')->andReturn($created);
        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(5)->andReturn($fresh);

        $dto    = new ServiceDTO('Alinhamento', 80.00);
        $result = $this->serviceService->create($dto);

        $this->assertEquals('Alinhamento', $result->name);
        $this->assertEquals(80.00, $result->value);
    }

    public function test_update_service_existente(): void
    {
        $service     = new Service();
        $service->id = 1;

        $fresh = new Service();
        $fresh->setAttribute('name', 'Balanceamento');

        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($service, $fresh);
        $this->serviceRepository->shouldReceive('update')->with(1, Mockery::type('array'))->andReturn(true);

        $dto    = new ServiceDTO(name: 'Balanceamento');
        $result = $this->serviceService->update(1, $dto);

        $this->assertEquals('Balanceamento', $result->name);
    }

    public function test_update_service_inexistente_lanca_excecao(): void
    {
        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->serviceService->update(99, new ServiceDTO(name: 'x'));
    }

    public function test_delete_service_existente(): void
    {
        $service     = new Service();
        $service->id = 1;

        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($service);
        $this->serviceRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $this->serviceService->delete(1);
        $this->assertTrue(true);
    }

    public function test_delete_service_inexistente_lanca_excecao(): void
    {
        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->serviceService->delete(99);
    }

    public function test_delete_retorna_bool(): void
    {
        $service     = new Service();
        $service->id = 2;

        $this->serviceRepository->shouldReceive('findByIdIgnoringStatus')->with(2)->andReturn($service);
        $this->serviceRepository->shouldReceive('delete')->with(2)->andReturn(true);

        $result = $this->serviceService->delete(2);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
