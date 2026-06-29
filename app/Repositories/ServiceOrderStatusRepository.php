<?php

namespace App\Repositories;

use App\Models\ServiceOrderStatus;
use App\Repositories\Contracts\ServiceOrderStatusRepositoryInterface;

class ServiceOrderStatusRepository implements ServiceOrderStatusRepositoryInterface
{
    public function findById(int $id): ?ServiceOrderStatus
    {
        return ServiceOrderStatus::find($id);
    }
}
