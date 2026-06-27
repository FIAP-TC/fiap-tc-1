<?php

namespace Tests\Unit\Services;

use App\DTOs\Customer\CreateCustomerDTO;
use App\DTOs\Customer\UpdateCustomerDTO;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    private CustomerService             $customerService;
    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = Mockery::mock(CustomerRepositoryInterface::class);
        $this->customerService    = new CustomerService($this->customerRepository);
    }

    public function test_find_all_retorna_colecao(): void
    {
        $this->customerRepository->shouldReceive('findAll')->andReturn(new Collection());

        $result = $this->customerService->findAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_id_existente_retorna_customer(): void
    {
        $customer = new Customer();
        $customer->setAttribute('name', 'João');

        $this->customerRepository->shouldReceive('findById')->with(1)->andReturn($customer);

        $result = $this->customerService->findById(1);

        $this->assertEquals('João', $result->name);
    }

    public function test_find_by_id_inexistente_retorna_null(): void
    {
        $this->customerRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $result = $this->customerService->findById(99);

        $this->assertNull($result);
    }

    public function test_create_retorna_customer_com_id(): void
    {
        $created     = new Customer();
        $created->id = 5;

        $fresh = new Customer();
        $fresh->setAttribute('name', 'Maria');
        $fresh->setAttribute('email', 'maria@example.com');

        $this->customerRepository->shouldReceive('create')->andReturn($created);
        $this->customerRepository->shouldReceive('findById')->with(5)->andReturn($fresh);

        $dto    = new CreateCustomerDTO('Maria', 'CPF', 12345678901, 'maria@example.com');
        $result = $this->customerService->create($dto);

        $this->assertEquals('Maria', $result->name);
    }

    public function test_update_customer_existente(): void
    {
        $customer     = new Customer();
        $customer->id = 1;

        $fresh = new Customer();
        $fresh->setAttribute('name', 'João Atualizado');

        $this->customerRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($customer, $fresh);
        $this->customerRepository->shouldReceive('update')->with(1, Mockery::type('array'))->andReturn(true);

        $dto    = new UpdateCustomerDTO(name: 'João Atualizado');
        $result = $this->customerService->update(1, $dto);

        $this->assertEquals('João Atualizado', $result->name);
    }

    public function test_update_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->customerService->update(99, new UpdateCustomerDTO(name: 'x'));
    }

    public function test_delete_customer_existente(): void
    {
        $customer     = new Customer();
        $customer->id = 1;

        $this->customerRepository->shouldReceive('findByIdIgnoringStatus')->with(1)->andReturn($customer);
        $this->customerRepository->shouldReceive('delete')->with(1)->andReturn(true);

        $this->customerService->delete(1);
        $this->assertTrue(true);
    }

    public function test_delete_customer_inexistente_lanca_excecao(): void
    {
        $this->customerRepository->shouldReceive('findByIdIgnoringStatus')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->customerService->delete(99);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
