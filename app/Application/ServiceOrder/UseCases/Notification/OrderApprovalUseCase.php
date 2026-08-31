<?php

namespace App\Application\ServiceOrder\UseCases\Notification;

use App\Domain\ServiceOrder\Notifications\OrderApprovalNotifierInterface;
use App\Domain\ServiceOrder\Security\OrderApprovalTokenSigner;

final class OrderApprovalUseCase
{
    public function __construct(
        private readonly OrderApprovalTokenSigner $tokenSigner,
        private readonly OrderApprovalNotifierInterface $notifier,
    ) {}

    public function requestApproval(string $to, int $serviceOrderId, int $customerId): void
    {
        $token = $this->tokenSigner->generate($serviceOrderId, $customerId);

        $this->notifier->send(
            to: $to,
            serviceOrderId: $serviceOrderId,
            token: $token,
        );
    }
}