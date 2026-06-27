<?php

namespace App\Services;

use App\DTOs\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function listAll(): Collection
    {
        return $this->userRepository->findAll();
    }

    public function findById(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \RuntimeException("Usuário #{$id} não encontrado.", 404);
        }

        return $user;
    }

    public function create(UserDTO $dto): User
    {
        $this->ensureRoleExists((int) $dto->roleId);

        $user = $this->userRepository->create([
            'username'    => $dto->username,
            'password'    => Hash::make((string) $dto->password),
            'role_id'     => $dto->roleId,
            'status'      => $dto->status ?? true,
            'create_date' => now()->toDateTimeString(),
        ]);

        return $this->userRepository->findById($user->id);
    }

    public function update(int $id, UserDTO $dto): User
    {
        $this->findById($id);

        if ($dto->roleId !== null) {
            $this->ensureRoleExists($dto->roleId);
        }

        $data = $dto->toArray();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepository->update($id, $data);

        return $this->userRepository->findById($id);
    }

    public function delete(int $id): void
    {
        $this->findById($id);
        $this->userRepository->delete($id);
    }

    private function ensureRoleExists(int $roleId): void
    {
        if (!$this->roleRepository->findById($roleId)) {
            throw new \RuntimeException("Role #{$roleId} não encontrada.", 422);
        }
    }
}
