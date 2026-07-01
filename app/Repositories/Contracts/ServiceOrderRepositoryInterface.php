<?php

namespace App\Repositories\Contracts;

use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;

interface ServiceOrderRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?ServiceOrder;
    public function findByIdIgnoringStatus(int $id): ?ServiceOrder;
    public function create(array $data): ServiceOrder;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findWithCurrentStatus(int $orderId): ?ServiceOrder;

    /** Insere o histórico de status inicial na tabela pivot. */
    public function createStatusHistory(int $orderId, int $statusId, int $customerId, int $usersId, int $usersRoleId): void;

    /** Insere produtos na tabela pivot service_order_has_products. */
    public function addProducts(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $products): void;

    /** Insere serviços na tabela pivot service_order_has_services. */
    public function addServices(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $services): void;

    /** Recalcula e retorna o valor total somando pivots de produtos e serviços. */
    public function calculateOrderTotal(int $orderId): float;

    /** Atualiza o valor total da ordem. */
    public function updateOrderValue(int $orderId, float $value): bool;

    public function getStatusHistory(int $orderId): Collect;
}
