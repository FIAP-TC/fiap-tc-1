<?php

namespace App\Infrastructure\Notifications;

use App\Domain\ServiceOrder\Notifications\OrderApprovalNotifierInterface;
use Illuminate\Support\Facades\Mail;

class OrderApprovalMailNotifier implements OrderApprovalNotifierInterface
{
    public function send(string $to, int $serviceOrderId, string $token): void
    {
        Mail::to($to)->send(
            new OrderApprovalMail(
                serviceOrderId: $serviceOrderId,
                token: $token,
            )
        );
    }
}
