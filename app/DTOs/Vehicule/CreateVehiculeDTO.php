<?php

namespace App\DTOs\Vehicule;

class CreateVehiculeDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $plate,
        public readonly string $model,
        public readonly string $brand,
        public readonly int    $years,
        public readonly int    $customerId,
        public readonly bool   $status = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:       $data['name'],
            plate:      $data['plate'],
            model:      $data['model'],
            brand:      $data['brand'],
            years:      (int) $data['years'],
            customerId: (int) $data['customer_id'],
            status:     (bool) ($data['status'] ?? true),
        );
    }
}
