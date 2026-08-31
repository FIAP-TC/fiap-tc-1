<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\Service\Exceptions\ServiceNotFoundException;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;

final class FindServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function execute(int $id): ServiceEntity
    {
        $service = $this->serviceRepository->findById($id);

        if (!$service) {
            throw ServiceNotFoundException::withId($id);
        }

        return $service;
    }
}
