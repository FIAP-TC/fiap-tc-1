<?php

namespace App\Application\Vehicule\UseCases;

use App\Application\Vehicule\DTOs\VehiculeDTO;
use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Domain\Vehicule\Exceptions\VehiculeNotFoundException;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class UpdateVehiculeUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(int $vehiculeId, VehiculeDTO $vehiculeDTO): VehiculeEntity
    {
        $vehicule = $this->vehiculeRepository->findById($vehiculeId);
        if (!$vehicule) {
            throw new VehiculeNotFoundException();
        }

        $vehicule = $this->vehiculeRepository->update($vehiculeId, $vehiculeDTO->toArray());
        if (!$vehicule) {
            throw VehiculeNotFoundException::withId($vehiculeId);
        }

        return $vehicule;
    }
}