<?php

namespace App\Domain\ServiceOrder\Notifications;

interface OrderApprovalNotifierInterface
{
    public function send(string $to, int $serviceOrderId, string $token): void;
}
