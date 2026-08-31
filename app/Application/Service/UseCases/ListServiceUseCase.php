<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;

final class ListServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function execute(): array
    {
        return $this->serviceRepository->findAll();
    }
}
