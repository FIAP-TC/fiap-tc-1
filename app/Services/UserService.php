<?php

namespace App\Services;

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Serviço de gerenciamento de usuários.
 *
 * Toda regra de negócio do domínio de usuários fica aqui:
 * - Hashing de senha (nunca o Repository faz isso)
 * - Validação de existência de role antes de criar/atualizar
 * - Controle de timestamps do schema customizado (create_date/modified_date)
 *
 * O Service não sabe como os dados são persistidos — delega ao Repository.
 */
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

    /**
     * @throws \RuntimeException quando o usuário não existe
     */
    public function findById(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \RuntimeException("Usuário #{$id} não encontrado.", 404);
        }

        return $user;
    }

    /**
     * Cria um novo usuário.
     *
     * A senha é hasheada aqui, na camada de negócio, antes de chegar ao Repository.
     * O Repository recebe apenas dados prontos para persistência.
     *
     * @throws \RuntimeException quando a role informada não existe
     */
    public function create(CreateUserDTO $dto): User
    {
        $this->ensureRoleExists($dto->roleId);

        $user = $this->userRepository->create([
            'username'    => $dto->username,
            'password'    => Hash::make($dto->password),
            'role_id'     => $dto->roleId,
            'status'      => $dto->status ? 1 : 0,
            'create_date' => now()->toDateTimeString(),
        ]);

        // Busca via repositório (e não $user->load) para manter o acesso
        // a dados sempre passando pelo Repository — testável via mock.
        return $this->userRepository->findById($user->id);
    }

    /**
     * Atualiza parcialmente um usuário (campos não informados são ignorados).
     *
     * @throws \RuntimeException quando o usuário ou a nova role não existem
     */
    public function update(int $id, UpdateUserDTO $dto): User
    {
        $this->findById($id);

        if ($dto->roleId !== null) {
            $this->ensureRoleExists($dto->roleId);
        }

        $data = $dto->toArray();

        // Senha: faz o hash apenas se foi informada no update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepository->update($id, $data);

        return $this->userRepository->findById($id);
    }

    /**
     * @throws \RuntimeException quando o usuário não existe
     */
    public function delete(int $id): void
    {
        $this->findById($id);
        $this->userRepository->delete($id);
    }

    /** Garante que a role informada existe no banco antes de associar ao usuário */
    private function ensureRoleExists(int $roleId): void
    {
        if (!$this->roleRepository->findById($roleId)) {
            throw new \RuntimeException("Role #{$roleId} não encontrada.", 422);
        }
    }
}
