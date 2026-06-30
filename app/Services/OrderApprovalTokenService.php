<?php

namespace App\Services;

use Carbon\Carbon;

class OrderApprovalTokenService
{
    public function __construct(
        private readonly string $secretKey, 
    ) {}

    public function generate(int $serviceOrderId, int $customerId): string {
        $expires = now()->addHours(24)->timestamp;

        $payload = implode('|', [
            $serviceOrderId,
            $customerId,
            $expires,
        ]);

        $signature = hash_hmac('sha256', $payload, $this->secretKey);
        return base64_encode($payload).'.'.$signature;
    }

    public function validate(string $token): array
    {
        [$payload, $signature] = explode('.', $token);

        $payload = base64_decode($payload);
        $expected = hash_hmac('sha256', $payload, $this->secretKey);

        if (!hash_equals($expected, $signature)) {
            throw new \RuntimeException('Invalid token.');
        }

        [$serviceOrderId, $customerId, $expires] = explode('|', $payload);
        $expiresAt = Carbon::createFromTimestamp((int) $expires);
        
        if (now()->isAfter($expiresAt)) {
            throw new \RuntimeException('Expired token.');
        }

        return [
            'service_order_id' => (int) $serviceOrderId,
            'customer_id' => (int) $customerId,
        ];
    }
}