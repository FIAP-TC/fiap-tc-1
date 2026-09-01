<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;

final class GetServiceOrderTrackingUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
    ) {}

    public function execute(int $orderId): ServiceOrderEntity
    {
        $order = $this->serviceOrderRepository->findWithCurrentStatus($orderId);

        if (!$order) {
            throw ServiceOrderNotFoundException::withId($orderId);
        }

        return $order;
    }
}
