<?php

namespace App\Services;

use App\DTOs\ServiceOrder\ServiceOrderDTO;
use App\DTOs\ServiceOrder\ServiceOrderItemsDTO;
use App\Entities\ServiceOrderEntity;
use App\Models\ServiceOrder;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Camada de negócio do módulo de Ordem de Serviço.
 *
 * Fluxo de criação:
 *   1. Valida veículo existente (via VehiculeRepository já injetado pelo controller? Não —
 *      o FormRequest valida via exists:vehicules,id. O Service confia nos dados validados.)
 *   2. Cria o registro em service_order com status=true e order_value=0.
 *   3. Insere o status inicial "Recebida" (ID=1) em service_order_has_service_order_status.
 *   4. Tudo dentro de DB::transaction() para garantir consistência.
 *
 * Fluxo de addItems:
 *   1. Valida que a ordem existe.
 *   2. Valida que todos os produtos/serviços informados existem.
 *   3. Insere nas tabelas pivot (insertOrIgnore — ignora duplicatas silenciosamente).
 *   4. Recalcula order_value somando todos os charged_values das pivots.
 *   5. Tudo dentro de DB::transaction().
 */
class ServiceOrderService
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly ProductRepositoryInterface      $productRepository,
        private readonly ServiceRepositoryInterface      $serviceRepository,
        private readonly VehiculeRepositoryInterface     $vehiculeRepository,
    ) {}

    public function findAll(): Collection
    {
        return $this->serviceOrderRepository->findAll();
    }

    public function findById(int $id): ?ServiceOrder
    {
        return $this->serviceOrderRepository->findById($id);
    }

    /**
     * Cria a ordem e registra o status inicial "Recebida" em uma única transação.
     * O valor inicial é 0 — será atualizado quando produtos/serviços forem adicionados.
     */
    public function create(ServiceOrderDTO $dto): ServiceOrder
    {
        $vehicule = $this->vehiculeRepository->findByIdIgnoringStatus($dto->vehiculesId);

        if (!$vehicule) {
            throw new \RuntimeException("Veículo #{$dto->vehiculesId} não encontrado.", 422);
        }

        $customerId = $vehicule->customer_id;

        return DB::transaction(function () use ($dto, $customerId) {
            $order = $this->serviceOrderRepository->create([
                'users_id'      => $dto->usersId,
                'users_role_id' => $dto->usersRoleId,
                'vehicules_id'  => $dto->vehiculesId,
                'order_value'   => 0.00,
                'time_average'  => $dto->timeAverage,
                'status'        => true,
                'create_date'   => now()->toDateTimeString(),
            ]);

            $this->serviceOrderRepository->createStatusHistory(
                $order->id,
                ServiceOrderEntity::STATUS_RECEBIDA,
                $customerId,
                $dto->usersId,
                $dto->usersRoleId,
            );

            return $this->serviceOrderRepository->findByIdIgnoringStatus($order->id);
        });
    }

    /**
     * Adiciona produtos e/ou serviços a uma ordem existente e recalcula o total.
     *
     * Regras:
     * - A ordem deve existir (ativa ou inativa — permite adicionar antes de reativar).
     * - Todos os IDs de produtos e serviços devem existir e estar ativos.
     * - Duplicatas são ignoradas (insertOrIgnore) — o mesmo item não é cobrado duas vezes.
     * - O valor total é recalculado APÓS a inserção, considerando todos os itens acumulados.
     */
    public function addItems(int $orderId, ServiceOrderItemsDTO $dto): ServiceOrder
    {
        $order = $this->ensureOrderExists($orderId);

        $products = $this->validateProducts($dto->productIds);
        $services = $this->validateServices($dto->serviceIds);

        $vehicule   = $this->vehiculeRepository->findByIdIgnoringStatus($order->vehicules_id);
        $customerId = $vehicule ? $vehicule->customer_id : 0;

        return DB::transaction(function () use ($order, $products, $services, $customerId) {
            if ($products->isNotEmpty()) {
                $this->serviceOrderRepository->addProducts(
                    $order->id,
                    $customerId,
                    $order->users_id,
                    $order->users_role_id,
                    $products->all(),
                );
            }

            if ($services->isNotEmpty()) {
                $this->serviceOrderRepository->addServices(
                    $order->id,
                    $customerId,
                    $order->users_id,
                    $order->users_role_id,
                    $services->all(),
                );
            }

            // Recalcula considerando TODOS os itens da ordem (inclusive os previamente inseridos)
            $total = $this->serviceOrderRepository->calculateOrderTotal($order->id);
            $this->serviceOrderRepository->updateOrderValue($order->id, $total);

            return $this->serviceOrderRepository->findByIdIgnoringStatus($order->id);
        });
    }

    /**
     * Soft-delete: mantém a ordem no banco com status=false,
     * preservando o histórico de status e os itens relacionados.
     */
    public function delete(int $id): bool
    {
        $this->ensureOrderExists($id);

        return $this->serviceOrderRepository->delete($id);
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function ensureOrderExists(int $id): ServiceOrder
    {
        $order = $this->serviceOrderRepository->findByIdIgnoringStatus($id);

        if (!$order) {
            throw new \RuntimeException("Ordem de Serviço #{$id} não encontrada.", 404);
        }

        return $order;
    }

    /**
     * Valida que todos os IDs de produto informados existem e estão ativos.
     * Lança RuntimeException listando os IDs ausentes.
     */
    private function validateProducts(array $ids): Collection
    {
        if (empty($ids)) {
            return new Collection();
        }

        $found   = $this->productRepository->findManyByIds($ids);
        $missing = array_diff($ids, $found->pluck('id')->toArray());

        if (!empty($missing)) {
            throw new \RuntimeException(
                'Produtos não encontrados: ' . implode(', ', $missing) . '.',
                422
            );
        }

        return $found;
    }

    /**
     * Valida que todos os IDs de serviço informados existem e estão ativos.
     * Lança RuntimeException listando os IDs ausentes.
     */
    private function validateServices(array $ids): Collection
    {
        if (empty($ids)) {
            return new Collection();
        }

        $found   = $this->serviceRepository->findManyByIds($ids);
        $missing = array_diff($ids, $found->pluck('id')->toArray());

        if (!empty($missing)) {
            throw new \RuntimeException(
                'Serviços não encontrados: ' . implode(', ', $missing) . '.',
                422
            );
        }

        return $found;
    }
}
