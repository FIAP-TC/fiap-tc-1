<?php

namespace App\DTOs\User;

class UserDTO
{
    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?int    $roleId   = null,
        public readonly ?bool   $status   = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            roleId:   isset($data['role_id']) ? (int) $data['role_id'] : null,
            status:   isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'username'      => $this->username,
            'password'      => $this->password,
            'role_id'       => $this->roleId,
            'status'        => $this->status,
            'modified_date' => now()->toDateTimeString(),
        ], fn($v) => $v !== null);
    }
}
