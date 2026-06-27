<?php

namespace App\DTOs\Product;

class ProductDTO
{
    public function __construct(
        public readonly ?string $name     = null,
        public readonly ?string $type     = null,
        public readonly ?float  $value    = null,
        public readonly ?int    $quantity = null,
        public readonly ?bool   $status   = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            value: isset($data['value']) ? (float) $data['value'] : null,
            quantity: isset($data['quantity']) ? (int) $data['quantity'] : null,
            status: isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'type'          => $this->type,
            'value'         => $this->value,
            'quantity'      => $this->quantity,
            'status'        => $this->status,
            'create_date'   => now()->toDateTimeString(),
            'modified_date' => now()->toDateTimeString(),
        ], fn ($value) => $value !== null);
    }
}