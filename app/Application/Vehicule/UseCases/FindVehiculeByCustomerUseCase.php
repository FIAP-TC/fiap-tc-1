<?php

namespace App\Application\Vehicule\UseCases;

use App\Domain\Vehicule\Exceptions\VehiculeNotFoundException;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class FindVehiculeByCustomerUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(int $customerId): array
    {
        $vehicule = $this->vehiculeRepository->findByCustomer($customerId);

        if (!$vehicule) {
            throw VehiculeNotFoundException::notFound();
        }

        return $vehicule;
    }
}
