<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Domain\Customer\Entites\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

final class CreateCustomerUseCase
{
    public function __construct(
       private readonly CustomerRepositoryInterface $customerRepository, 
    ) {}

    public function execute(CustomerDTO $customerDTO): CustomerEntity
    {
        $customer = $this->customerRepository->create($customerDTO->toArray());
        return $customer;
    }
}