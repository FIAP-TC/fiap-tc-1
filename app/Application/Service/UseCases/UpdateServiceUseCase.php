<?php

namespace App\Application\Service\UseCases;

use App\Application\Service\DTOs\ServiceDTO;
use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\Service\Exceptions\ServiceNotFoundException;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;

final class UpdateServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function execute(int $id, ServiceDTO $data): ServiceEntity
    {
        $service = $this->serviceRepository->update($id, $data->toArray());

        if (!$service) {
            throw ServiceNotFoundException::withId($id);
        }

        return $service;
    }
}
