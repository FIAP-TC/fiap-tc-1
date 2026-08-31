<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;

final class ListServiceOrdersUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
    ) {}

    public function execute(): array
    {
        return $this->serviceOrderRepository->findAll();
    }
}
