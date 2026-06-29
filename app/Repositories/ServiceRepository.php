<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Retorna apenas serviços ativos (status = true).
     */
    public function findAll(): Collection
    {
        return Service::where('status', true)->get();
    }

    /**
     * Busca por ID filtrando status ativo — usado em leituras públicas.
     */
    public function findById(int $id): ?Service
    {
        return Service::where('status', true)->find($id);
    }

    /**
     * Busca por ID sem filtro de status — usado em update/delete
     * para permitir reativação de registros inativados.
     */
    public function findByIdIgnoringStatus(int $id): ?Service
    {
        return Service::find($id);
    }

    public function findManyByIds(array $ids): Collection
    {
        return Service::where('status', true)->whereIn('id', $ids)->get();
    }

    public function create(array $data): Service
    {
        return Service::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) Service::where('id', $id)->update($data);
    }

    /**
     * Soft-delete via coluna status: mantém o histórico no banco
     * e permite reativação futura via PUT com status=true.
     */
    public function delete(int $id): bool
    {
        return (bool) Service::where('id', $id)->update(['status' => false]);
    }
}
