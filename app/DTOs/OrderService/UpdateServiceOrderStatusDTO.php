<?php

namespace App\DTOs\OrderService;

class UpdateServiceOrderStatusDTO
{
    public function __construct(
        public readonly int $statusId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            statusId: (int) $data['status_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'service_order_status_id' => $this->statusId,
            'modified_date' => now()->toDateTimeString(),
        ];
    }
}