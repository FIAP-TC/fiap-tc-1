<?php

namespace App\DTOs\Customer;

class CreateCustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $identification,
        public readonly int    $identificationNumber,
        public readonly string $email,
        public readonly bool   $status = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:                 $data['name'],
            identification:       $data['identification'],
            identificationNumber: (int) $data['identification_number'],
            email:                $data['email'],
            status:               (bool) ($data['status'] ?? true),
        );
    }
}
