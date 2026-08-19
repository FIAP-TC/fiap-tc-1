<?php

namespace App\Application\Customer\DTOs;

class CustomerDTO
{
    public function __construct(
        public readonly ?string $name                 = null,
        public readonly ?string $identification       = null,
        public readonly ?int    $identificationNumber = null,
        public readonly ?string $email                = null,
        public readonly ?bool   $status               = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:                 $data['name'] ?? null,
            identification:       $data['identification'] ?? null,
            identificationNumber: isset($data['identification_number']) ? (int) $data['identification_number'] : null,
            email:                $data['email'] ?? null,
            status:               isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'                  => $this->name,
            'identification'        => $this->identification,
            'identification_number' => $this->identificationNumber,
            'email'                 => $this->email,
            'status'                => $this->status,
        ], fn($v) => $v !== null);
    }
}
