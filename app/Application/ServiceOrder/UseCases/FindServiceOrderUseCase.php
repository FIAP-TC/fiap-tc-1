<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;

final class FindServiceOrderUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
    ) {}

    public function execute(int $id): ServiceOrderEntity
    {
        $order = $this->serviceOrderRepository->findById($id);

        if (!$order) {
            throw ServiceOrderNotFoundException::withId($id);
        }

        return $order;
    }
}
