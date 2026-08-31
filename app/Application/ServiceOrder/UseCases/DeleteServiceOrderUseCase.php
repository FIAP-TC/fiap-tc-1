<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;

/**
 * Soft-delete: mantém a ordem no banco com status=false,
 * preservando o histórico de status e os itens relacionados.
 */
final class DeleteServiceOrderUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
    ) {}

    public function execute(int $id): bool
    {
        if (!$this->serviceOrderRepository->findByIdIgnoringStatus($id)) {
            throw ServiceOrderNotFoundException::withId($id);
        }

        return $this->serviceOrderRepository->delete($id);
    }
}
