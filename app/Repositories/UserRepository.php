<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementação Eloquent do repositório de usuários.
 *
 * Responsabilidade exclusiva: acesso a dados.
 * Nenhuma regra de negócio deve existir aqui.
 *
 * O eager loading da role é feito por padrão nos finders para evitar
 * N+1 queries quando listamos usuários com seus perfis.
 */
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $model) {}

    public function findAll(): Collection
    {
        return $this->model->with('role')->get();
    }

    public function findById(int $id): ?User
    {
        return $this->model->with('role')->find($id);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->model->with('role')->where('username', $username)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->where('id', $id)->delete();
    }
}
