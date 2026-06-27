<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato do repositório de usuários.
 *
 * Definir uma interface aqui permite:
 * 1. Testar Services mockando o repositório sem tocar no banco.
 * 2. Trocar a implementação (ex: Redis cache, outro ORM) sem alterar o Service.
 * 3. Seguir o Dependency Inversion Principle (SOLID).
 */
interface UserRepositoryInterface
{
    public function findAll(): Collection;

    public function findById(int $id): ?User;

    public function findByUsername(string $username): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
