<?php 

namespace App\Services\Contracts;

interface OrderApprovalNotificationServiceInterface
{
    public function send(string $to, int $serviceOrderId, string $token): void;
}