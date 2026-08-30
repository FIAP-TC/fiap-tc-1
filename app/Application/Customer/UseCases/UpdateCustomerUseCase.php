<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Domain\Customer\Entites\CustomerEntity;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

final class UpdateCustomerUseCase
{
    public function __construct(
       private readonly CustomerRepositoryInterface $customerRepository, 
    ) {}

    public function execute(int $customerId, CustomerDTO $customerDTO): CustomerEntity
    {
        $customer = $this->customerRepository->update($customerId, $customerDTO->toArray());
        
        if (!$customer) {
            throw CustomerNotFoundException::withId($customerId);
        }

        return $customer;
    }
}