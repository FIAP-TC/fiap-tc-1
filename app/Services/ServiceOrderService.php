<?php

namespace App\Services;

use App\DTOs\OrderService\UpdateServiceOrderStatusDTO;
use App\Enums\ServiceOrderStatusEnum;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;

class ServiceOrderService
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly OrderApprovalTokenService $tokenService,
        private readonly OrderApprovalNotificationService $notificationService,
    ) {}

    public function updateStatus(int $serviceOrderId, UpdateServiceOrderStatusDTO $dto): void
    {
        $serviceOrder = $this->serviceOrderRepository->findById($serviceOrderId);

        if (!$serviceOrder) {
            throw new \RuntimeException('Service order not found.');
        }

        $this->serviceOrderRepository->update($serviceOrder->id,$dto->toArray());

        if ($dto->statusId === ServiceOrderStatusEnum::WAITING_APPROVAL->value) {
            $token = $this->tokenService->generate($serviceOrderId, $serviceOrder->customerId);
            $this->notificationService->send(
                $serviceOrder->email,
                $serviceOrderId, 
                $token,
            );
        }
    }

    public function approve(int $orderId): void
    {

    }

    public function reject(int $orderId): void
    {

    }
}
