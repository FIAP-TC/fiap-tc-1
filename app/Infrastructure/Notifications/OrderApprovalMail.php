<?php

namespace App\Infrastructure\Notifications;

use Illuminate\Mail\Mailable;

class OrderApprovalMail extends Mailable
{
    public function __construct(
        public readonly int $serviceOrderId,
        public readonly string $token,
    ) {}

    public function build()
    {
        return $this
            ->subject("Service Order #{$this->serviceOrderId}")
            ->view('emails.order-approval');
    }
}
