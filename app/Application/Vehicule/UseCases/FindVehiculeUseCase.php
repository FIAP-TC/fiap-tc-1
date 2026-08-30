<?php

namespace App\Application\Vehicule\UseCases;

use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Domain\Vehicule\Exceptions\VehiculeNotFoundException;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class FindVehiculeUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(int $id): VehiculeEntity
    {
        $vehicule = $this->vehiculeRepository->findById($id);

        if (!$vehicule) {
            throw VehiculeNotFoundException::withId($id);
        }

        return $vehicule;
    }
}