<?php

namespace App\DTOs\Vehicule;

class VehiculeDTO
{
    public function __construct(
        public readonly ?string $name       = null,
        public readonly ?string $plate      = null,
        public readonly ?string $model      = null,
        public readonly ?string $brand      = null,
        public readonly ?int    $years      = null,
        public readonly ?bool   $status     = null,
        public readonly ?int    $customerId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:       $data['name'] ?? null,
            plate:      $data['plate'] ?? null,
            model:      $data['model'] ?? null,
            brand:      $data['brand'] ?? null,
            years:      isset($data['years']) ? (int) $data['years'] : null,
            status:     isset($data['status']) ? (bool) $data['status'] : null,
            customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'plate'         => $this->plate,
            'model'         => $this->model,
            'brand'         => $this->brand,
            'years'         => $this->years,
            'status'        => $this->status,
            'customer_id'   => $this->customerId,
            'modified_date' => now()->toDateTimeString(),
        ], fn($v) => $v !== null);
    }
}
