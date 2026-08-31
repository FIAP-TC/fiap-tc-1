<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Application\ServiceOrder\DTOs\UpdateServiceOrderStatusDTO;
use App\Application\ServiceOrder\UseCases\Notification\OrderApprovalUseCase;
use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class UpdateServiceOrderStatusUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
        private readonly OrderApprovalUseCase $orderApprovalUseCase,
    ) {}

    public function execute(int $serviceOrderId, UpdateServiceOrderStatusDTO $dto): void
    {
        $serviceOrder = $this->serviceOrderRepository->findById($serviceOrderId);

        if (!$serviceOrder) {
            throw ServiceOrderNotFoundException::withId($serviceOrderId);
        }

        $vehicule = $this->vehiculeRepository->findByIdIgnoringStatus($serviceOrder->getVehiculesId());

        DB::transaction(function () use ($dto, $serviceOrder, $vehicule) {
            $this->serviceOrderRepository->createStatusHistory(
                $serviceOrder->getId(),
                $dto->statusId,
                $vehicule->getCustomerId(),
                $serviceOrder->getUsersId(),
                $serviceOrder->getUsersRoleId(),
            );

            $average = $this->calculateAverageTime($serviceOrder->getId());

            $this->serviceOrderRepository->update($serviceOrder->getId(), [
                'time_average' => $average,
            ]);

            if ($dto->statusId === ServiceOrderEntity::STATUS_AGUARDANDO_APROVACAO) {
                $this->orderApprovalUseCase->requestApproval(
                    to: $vehicule->getCustomer()->getEmail(),
                    serviceOrderId: $serviceOrder->getId(),
                    customerId: $vehicule->getCustomerId(),
                );
            }
        });
    }

    private function calculateAverageTime(int $orderId): float
    {
        $history = $this->serviceOrderRepository->findByIdIgnoringStatus($orderId)->getStatusHistory();

        if (count($history) < 2) {
            return 0;
        }

        $totalMinutes = 0;

        for ($i = 1; $i < count($history); $i++) {
            $previous = $history[$i - 1]->getCreatedAt();
            $current = $history[$i]->getCreatedAt();

            $totalMinutes += ($current->getTimestamp() - $previous->getTimestamp()) / 60;
        }

        return round($totalMinutes / (count($history) - 1), 2);
    }
}
