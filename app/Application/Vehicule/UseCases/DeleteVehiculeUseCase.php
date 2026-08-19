<?php

namespace App\Application\Vehicule\UseCases;

use App\Domain\Vehicule\Exceptions\VehiculeNotFoundException;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class DeleteVehiculeUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(int $vehiculeId): bool
    {
        $vehicule = $this->vehiculeRepository->delete($vehiculeId);

        if (!$vehicule) {
            throw VehiculeNotFoundException::withId($vehiculeId);
        }

        return true;
    }
}
