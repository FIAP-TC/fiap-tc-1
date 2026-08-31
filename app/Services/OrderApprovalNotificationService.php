<?php

namespace App\Services;

use App\Infrastructure\Notifications\OrderApprovalMail;
use App\Services\Contracts\OrderApprovalNotificationServiceInterface;
use Illuminate\Support\Facades\Mail;

class OrderApprovalNotificationService implements OrderApprovalNotificationServiceInterface
{
    public function send(string $to, int $serviceOrderId, string $token): void {
        Mail::to($to)->send(
            new OrderApprovalMail(
                serviceOrderId: $serviceOrderId,
                token: $token,
            )
        );
    }
}