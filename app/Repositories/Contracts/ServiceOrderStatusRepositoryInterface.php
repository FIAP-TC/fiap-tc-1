<?php

namespace App\Repositories\Contracts;

use App\Models\ServiceOrderStatus;

interface ServiceOrderStatusRepositoryInterface
{
    public function findById(int $id): ?ServiceOrderStatus;
}