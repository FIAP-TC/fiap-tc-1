<?php

namespace App\Domain\ServiceOrder\Repositories;

use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;

interface ServiceOrderRepositoryInterface
{
    /** @return ServiceOrderEntity[] */
    public function findAll(): array;

    public function findById(int $id): ?ServiceOrderEntity;

    /** Ignora o filtro de status ativo — usado em update/delete/addItems para reativar ordens. */
    public function findByIdIgnoringStatus(int $id): ?ServiceOrderEntity;

    /** Retorna a ordem com o status atual (última entrada do histórico) carregado. */
    public function findWithCurrentStatus(int $orderId): ?ServiceOrderEntity;

    public function create(array $data): ServiceOrderEntity;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;

    public function createStatusHistory(int $orderId, int $statusId, int $customerId, int $usersId, int $usersRoleId): void;

    /** @param ProductEntity[] $products */
    public function addProducts(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $products): void;

    /** @param ServiceEntity[] $services */
    public function addServices(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $services): void;

    public function calculateOrderTotal(int $orderId): float;
    public function updateOrderValue(int $orderId, float $value): bool;
}
