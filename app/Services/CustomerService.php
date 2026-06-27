<?php

namespace App\Services;

use App\DTOs\Customer\CustomerDTO;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function findAll(): Collection
    {
        return $this->customerRepository->findAll();
    }

    public function findById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function create(CustomerDTO $dto): Customer
    {
        $customer = $this->customerRepository->create(array_merge(
            ['status' => true],
            $dto->toArray(),
            ['create_date' => now()->toDateTimeString()]
        ));

        return $this->customerRepository->findByIdIgnoringStatus($customer->id);
    }

    public function update(int $id, CustomerDTO $dto): Customer
    {
        $this->ensureCustomerExists($id);

        $this->customerRepository->update($id, $dto->toArray());

        return $this->customerRepository->findByIdIgnoringStatus($id);
    }

    public function delete(int $id): bool
    {
        $this->ensureCustomerExists($id);

        return $this->customerRepository->delete($id);
    }

    private function ensureCustomerExists(int $id): void
    {
        if (!$this->customerRepository->findByIdIgnoringStatus($id)) {
            throw new \RuntimeException("Customer #{$id} not found.");
        }
    }
}
