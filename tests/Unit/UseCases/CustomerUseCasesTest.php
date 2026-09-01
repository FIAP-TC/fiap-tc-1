<?php

namespace Tests\Unit\UseCases;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Application\Customer\UseCases\CreateCustomerUseCase;
use App\Application\Customer\UseCases\DeleteCustomerUseCase;
use App\Application\Customer\UseCases\FindCustomerUseCase;
use App\Application\Customer\UseCases\ListCustomersUseCases;
use App\Application\Customer\UseCases\UpdateCustomerUseCase;
use App\Domain\Customer\Entites\CustomerEntity;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use Mockery;
use Tests\TestCase;

class CustomerUseCasesTest extends TestCase
{
    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = Mockery::mock(CustomerRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeCustomer(string $name = 'João', ?int $id = 1): CustomerEntity
    {
        return new CustomerEntity(
            id: $id,
            name: $name,
            identification: 'CPF',
            identificationNumber: 12345678901,
            email: 'joao@example.com',
        );
    }

    public function test_list_retorna_array_de_customers(): void
    {
        $this->customerRepository->shouldReceive('findAll')->andReturn([$this->makeCustomer()]);

        $result = (new ListCustomersUseCases($this->customerRepository))->execute();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_find_by_id_existente_retorna_customer(): void
    {
        $this->customerRepository->shouldReceive('findById')->with(1)->andReturn($this->makeCustomer('João'));

        $result = (new FindCustomerUseCase($this->customerRepository))->execute(1);

        $this->assertEquals('João', $result->getName());
    }

    public function test_find_by_id_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(CustomerNotFoundException::class);

        (new FindCustomerUseCase($this->customerRepository))->execute(99);
    }

    public function test_create_retorna_customer_com_id(): void
    {
        $this->customerRepository->shouldReceive('create')->andReturn($this->makeCustomer('Maria', 5));

        $dto = new CustomerDTO(
            name: 'Maria',
            identification: 'CPF',
            identificationNumber: 12345678901,
            email: 'maria@example.com',
        );

        $result = (new CreateCustomerUseCase($this->customerRepository))->execute($dto);

        $this->assertEquals('Maria', $result->getName());
        $this->assertEquals(5, $result->getId());
    }

    public function test_update_customer_existente(): void
    {
        $this->customerRepository->shouldReceive('update')
            ->with(1, Mockery::type('array'))
            ->andReturn($this->makeCustomer('João Atualizado'));

        $result = (new UpdateCustomerUseCase($this->customerRepository))
            ->execute(1, new CustomerDTO(name: 'João Atualizado'));

        $this->assertEquals('João Atualizado', $result->getName());
    }

    public function test_update_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('update')->with(99, Mockery::type('array'))->andReturn(null);

        $this->expectException(CustomerNotFoundException::class);

        (new UpdateCustomerUseCase($this->customerRepository))->execute(99, new CustomerDTO(name: 'x'));
    }

    public function test_delete_customer_existente(): void
    {
        $this->customerRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $result = (new DeleteCustomerUseCase($this->customerRepository))->execute(1);

        $this->assertTrue($result);
    }

    public function test_delete_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('delete')->with(99)->andReturn(false);

        $this->expectException(CustomerNotFoundException::class);

        (new DeleteCustomerUseCase($this->customerRepository))->execute(99);
    }
}
