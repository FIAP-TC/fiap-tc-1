<?php 

namespace App\Repositories\Contracts;

interface ServiceOrderRepositoryInterface
{
    public function updateStatus(int $serviceOrderId, array $data): bool;
}