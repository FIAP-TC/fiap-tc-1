<?php

namespace App\Repositories;

use App\Repositories\Contracts\ServiceOrderRepositoryInterface;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function __construct(
        private readonly ServiceOrder $serviceOrderModel, 
    ) {}
    public function updateStatus(int $serviceOrderId, array $data): bool 
    {
        return $this->serviceOrderModel
            ->where('id', $serviceOrderId)
            ->update($data) > 0;
    }
}