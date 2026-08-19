<?php

namespace App\Application\Customer\UseCases;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

final class ListCustomersUseCases
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function execute(): array
    {
        return $this->customerRepository->findAll();
    }
}