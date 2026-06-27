<?php

namespace App\Services;

use App\DTOs\Vehicule\VehiculeDTO;
use App\Models\Vehicule;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VehiculeService
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function findAll(): Collection
    {
        return $this->vehiculeRepository->findAll();
    }

    public function findById(int $id): ?Vehicule
    {
        return $this->vehiculeRepository->findById($id);
    }

    public function findByCustomer(int $customerId): Collection
    {
        $this->ensureCustomerExists($customerId);

        return $this->vehiculeRepository->findByCustomer($customerId);
    }

    public function create(VehiculeDTO $dto): Vehicule
    {
        $this->ensureCustomerExists((int) $dto->customerId);

        $vehicule = $this->vehiculeRepository->create(array_merge(
            ['status' => true],
            $dto->toArray(),
            ['create_date' => now()->toDateTimeString()]
        ));

        return $this->vehiculeRepository->findByIdIgnoringStatus($vehicule->id);
    }

    public function update(int $id, VehiculeDTO $dto): Vehicule
    {
        $this->ensureVehiculeExists($id);

        if ($dto->customerId !== null) {
            $this->ensureCustomerExists($dto->customerId);
        }

        $this->vehiculeRepository->update($id, $dto->toArray());

        return $this->vehiculeRepository->findByIdIgnoringStatus($id);
    }

    public function delete(int $id): bool
    {
        $this->ensureVehiculeExists($id);

        return $this->vehiculeRepository->delete($id);
    }

    private function ensureVehiculeExists(int $id): void
    {
        if (!$this->vehiculeRepository->findByIdIgnoringStatus($id)) {
            throw new \RuntimeException("Vehicule #{$id} not found.");
        }
    }

    private function ensureCustomerExists(int $customerId): void
    {
        if (!$this->customerRepository->findByIdIgnoringStatus($customerId)) {
            throw new \RuntimeException("Customer #{$customerId} not found.");
        }
    }
}
