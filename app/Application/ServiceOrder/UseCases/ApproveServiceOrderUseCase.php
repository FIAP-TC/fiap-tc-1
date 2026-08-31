<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;
use App\Domain\ServiceOrder\Security\OrderApprovalTokenSigner;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class ApproveServiceOrderUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
        private readonly OrderApprovalTokenSigner $tokenSigner,
    ) {}

    public function execute(string $token): void
    {
        $payload = $this->tokenSigner->validate($token);

        $serviceOrder = $this->serviceOrderRepository->findById($payload['service_order_id']);

        if (!$serviceOrder) {
            throw ServiceOrderNotFoundException::withId($payload['service_order_id']);
        }

        $vehicule = $this->vehiculeRepository->findByIdIgnoringStatus($serviceOrder->getVehiculesId());

        DB::transaction(function () use ($serviceOrder, $vehicule) {
            $this->serviceOrderRepository->createStatusHistory(
                $serviceOrder->getId(),
                ServiceOrderEntity::STATUS_APROVADA_PELO_CLIENTE,
                $vehicule->getCustomerId(),
                $serviceOrder->getUsersId(),
                $serviceOrder->getUsersRoleId(),
            );
        });
    }
}
