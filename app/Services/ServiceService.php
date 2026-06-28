<?php

namespace App\Services;

use App\DTOs\Service\ServiceDTO;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Centraliza todas as regras de negócio do módulo de Serviços.
 *
 * Depende apenas da interface do repositório (Dependency Inversion),
 * facilitando testes via mock e eventual troca de ORM.
 */
class ServiceService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function findAll(): Collection
    {
        return $this->serviceRepository->findAll();
    }

    public function findById(int $id): ?Service
    {
        return $this->serviceRepository->findById($id);
    }

    /**
     * Cria um serviço garantindo status=true e create_date via array_merge,
     * sem precisar que o DTO carregue esses defaults.
     */
    public function create(ServiceDTO $dto): Service
    {
        $service = $this->serviceRepository->create(array_merge(
            ['status' => true],
            $dto->toArray(),
            ['create_date' => now()->toDateTimeString()],
        ));

        return $this->serviceRepository->findByIdIgnoringStatus($service->id);
    }

    /**
     * Atualiza um serviço — aceita registros inativos para permitir reativação
     * via status=true no payload.
     */
    public function update(int $id, ServiceDTO $dto): Service
    {
        $this->ensureServiceExists($id);

        $this->serviceRepository->update($id, $dto->toArray());

        return $this->serviceRepository->findByIdIgnoringStatus($id);
    }

    /**
     * Soft-delete: apenas muda status para false, preservando o histórico.
     */
    public function delete(int $id): bool
    {
        $this->ensureServiceExists($id);

        return $this->serviceRepository->delete($id);
    }

    /**
     * Garante que o serviço existe (ativo ou inativo) antes de operações de escrita.
     *
     * @throws \RuntimeException com código 404 quando não encontrado.
     */
    private function ensureServiceExists(int $id): void
    {
        if (!$this->serviceRepository->findByIdIgnoringStatus($id)) {
            throw new \RuntimeException("Serviço #{$id} não encontrado.", 404);
        }
    }
}
