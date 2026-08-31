<?php

namespace App\Application\Service\UseCases;

use App\Application\Service\DTOs\ServiceDTO;
use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;

final class CreateServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function execute(ServiceDTO $data): ServiceEntity
    {
        return $this->serviceRepository->create($data->toArray());
    }
}
