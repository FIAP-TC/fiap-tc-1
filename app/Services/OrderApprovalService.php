<?php

namespace App\Services;

use App\Services\Contracts\OrderApprovalNotificationServiceInterface;

class OrderApprovalService
{
    public function __construct(
        private readonly OrderApprovalTokenService $tokenService,
        private readonly OrderApprovalNotificationServiceInterface $notificationService,
    ) {}

    public function requestApproval(string $to, int $serviceOrderId, int $customerId): void
    {
        $token = $this->tokenService->generate($serviceOrderId, $customerId);

        $this->notificationService->send(
            $to,
            $serviceOrderId,
            $token,
        );
    }
}