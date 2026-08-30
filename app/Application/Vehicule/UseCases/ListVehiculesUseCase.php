<?php

namespace App\Application\Vehicule\UseCases;

use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;

final class ListVehiculesUseCase
{
    public function __construct(
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(): array
    {
        return $this->vehiculeRepository->findAll();
    }
}