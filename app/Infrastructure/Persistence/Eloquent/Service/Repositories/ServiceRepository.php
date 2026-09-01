<?php

namespace App\Infrastructure\Persistence\Eloquent\Service\Repositories;

use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\ServiceMapper;
use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function __construct(
        private readonly Service $serviceModel,
    ) {}

    /**
     * Retorna apenas serviços ativos (status = true).
     */
    public function findAll(): array
    {
        $models = $this->serviceModel
            ->where('status', true)
            ->get();

        return $models
            ->map(fn (Service $model) => ServiceMapper::toDomain($model))
            ->all();
    }

    /**
     * Busca por ID filtrando status ativo — usado em leituras públicas.
     */
    public function findById(int $id): ?ServiceEntity
    {
        $model = $this->serviceModel
            ->where('status', true)
            ->find($id);

        return $model ? ServiceMapper::toDomain($model) : null;
    }

    /**
     * Busca por ID sem filtro de status — usado em update/delete
     * para permitir reativação de registros inativados.
     */
    public function findByIdIgnoringStatus(int $id): ?ServiceEntity
    {
        $model = $this->serviceModel->find($id);

        return $model ? ServiceMapper::toDomain($model) : null;
    }

    public function findManyByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $models = $this->serviceModel
            ->where('status', true)
            ->whereIn('id', $ids)
            ->get();

        return $models
            ->map(fn (Service $model) => ServiceMapper::toDomain($model))
            ->all();
    }

    public function create(array $data): ServiceEntity
    {
        $model = $this->serviceModel->create($data);
        $model->refresh();

        return ServiceMapper::toDomain($model);
    }

    public function update(int $id, array $data): ?ServiceEntity
    {
        $model = $this->serviceModel->find($id);

        if (!$model) {
            return null;
        }

        $model->update($data);

        return ServiceMapper::toDomain($model);
    }

    /**
     * Soft-delete via coluna status: mantém o histórico no banco
     * e permite reativação futura via PUT com status=true.
     */
    public function delete(int $id): bool
    {
        $model = $this->serviceModel->find($id);

        if (!$model) {
            return false;
        }

        return (bool) $model->update(['status' => false]);
    }
}
