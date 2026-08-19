<?php

namespace App\Application\Vehicule\UseCases;

use App\Application\Vehicule\DTOs\VehiculeDTO;
use App\Domain\Vehicule\Entites\VehiculeEntity;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class CreateVehiculeUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(VehiculeDTO $vehiculeDTO): VehiculeEntity
    {
        $vehicule = $this->vehiculeRepository->create($vehiculeDTO->toArray());
        return $vehicule;
    }
}
