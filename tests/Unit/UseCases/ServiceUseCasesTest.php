<?php

namespace Tests\Unit\UseCases;

use App\Application\Service\DTOs\ServiceDTO;
use App\Application\Service\UseCases\CreateServiceUseCase;
use App\Application\Service\UseCases\DeleteServiceUseCase;
use App\Application\Service\UseCases\FindServiceUseCase;
use App\Application\Service\UseCases\ListServiceUseCase;
use App\Application\Service\UseCases\UpdateServiceUseCase;
use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\Service\Exceptions\ServiceNotFoundException;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use Mockery;
use Tests\TestCase;

class ServiceUseCasesTest extends TestCase
{
    private ServiceRepositoryInterface $serviceRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceRepository = Mockery::mock(ServiceRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeService(string $name = 'Troca de óleo', float $value = 80.00, ?int $id = 1): ServiceEntity
    {
        return new ServiceEntity(id: $id, name: $name, value: $value);
    }

    public function test_list_retorna_array_de_services(): void
    {
        $this->serviceRepository->shouldReceive('findAll')->andReturn([$this->makeService()]);

        $result = (new ListServiceUseCase($this->serviceRepository))->execute();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_find_by_id_existente_retorna_service(): void
    {
        $this->serviceRepository->shouldReceive('findById')->with(1)->andReturn($this->makeService('Troca de óleo'));

        $result = (new FindServiceUseCase($this->serviceRepository))->execute(1);

        $this->assertEquals('Troca de óleo', $result->getName());
    }

    public function test_find_by_id_inexistente_lanca_excecao(): void
    {
        $this->serviceRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(ServiceNotFoundException::class);

        (new FindServiceUseCase($this->serviceRepository))->execute(99);
    }

    public function test_create_retorna_service_com_id(): void
    {
        $this->serviceRepository->shouldReceive('create')->andReturn($this->makeService('Alinhamento', 80.00, 5));

        $result = (new CreateServiceUseCase($this->serviceRepository))->execute(new ServiceDTO(name: 'Alinhamento', value: 80.00));

        $this->assertEquals('Alinhamento', $result->getName());
        $this->assertEquals(80.00, $result->getValue());
        $this->assertEquals(5, $result->getId());
    }

    public function test_update_service_existente(): void
    {
        $this->serviceRepository->shouldReceive('update')
            ->with(1, Mockery::type('array'))
            ->andReturn($this->makeService('Balanceamento'));

        $result = (new UpdateServiceUseCase($this->serviceRepository))->execute(1, new ServiceDTO(name: 'Balanceamento'));

        $this->assertEquals('Balanceamento', $result->getName());
    }

    public function test_update_service_inexistente_lanca_excecao(): void
    {
        $this->serviceRepository->shouldReceive('update')->with(99, Mockery::type('array'))->andReturn(null);

        $this->expectException(ServiceNotFoundException::class);

        (new UpdateServiceUseCase($this->serviceRepository))->execute(99, new ServiceDTO(name: 'x'));
    }

    public function test_delete_service_existente(): void
    {
        $this->serviceRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $result = (new DeleteServiceUseCase($this->serviceRepository))->execute(1);

        $this->assertTrue($result);
    }

    public function test_delete_service_inexistente_lanca_excecao(): void
    {
        $this->serviceRepository->shouldReceive('delete')->with(99)->andReturn(false);

        $this->expectException(ServiceNotFoundException::class);

        (new DeleteServiceUseCase($this->serviceRepository))->execute(99);
    }
}
