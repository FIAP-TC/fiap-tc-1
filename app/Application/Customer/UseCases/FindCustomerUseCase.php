<?php

namespace App\Application\Customer\UseCases;

use App\Domain\Customer\Entites\CustomerEntity;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

final class FindCustomerUseCase
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function execute(int $customerId): CustomerEntity
    {
        $customer = $this->customerRepository->findById($customerId);

        if (!$customer) {
            throw CustomerNotFoundException::withId($customerId);
        }

        return $customer;
    }
}