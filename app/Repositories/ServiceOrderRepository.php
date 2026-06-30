<?php

namespace App\Repositories;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderStatus;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Support\Facades\DB;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function findAll(): Collection
    {
        return ServiceOrder::with(['user', 'vehicule'])
            ->where('status', true)
            ->get();
    }

    public function findById(int $id): ?ServiceOrder
    {
        return ServiceOrder::with(['user', 'vehicule'])
            ->where('status', true)
            ->find($id);
    }

    /**
     * Busca sem filtro de status para operações de escrita (update/delete/addItems),
     * permitindo reativar ordens inativadas.
     */
    public function findByIdIgnoringStatus(int $id): ?ServiceOrder
    {
        return ServiceOrder::with(['user', 'vehicule'])->find($id);
    }

    public function create(array $data): ServiceOrder
    {
        return ServiceOrder::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) ServiceOrder::where('id', $id)->update($data);
    }

    /**
     * Soft-delete: mantém o registro no banco com status=false,
     * preservando o histórico de ordens e seus relacionamentos.
     */
    public function delete(int $id): bool
    {
        return (bool) ServiceOrder::where('id', $id)->update(['status' => false]);
    }

    /**
     * Insere o primeiro registro de histórico de status.
     * Chamado imediatamente após a criação da ordem, dentro da mesma transação.
     *
     * A coluna service_order_customer_id é vestigial no schema original e não
     * possui FK ativa — usamos 0 como placeholder para satisfazer a PK composta.
     */
    public function createStatusHistory(int $orderId, int $statusId, int $customerId, int $usersId, int $usersRoleId): void
    {
        DB::table('service_order_has_service_order_status')->insert([
            'service_order_id'            => $orderId,
            'service_order_customer_id'   => $customerId,
            'service_order_users_id'      => $usersId,
            'service_order_users_role_id' => $usersRoleId,
            'service_order_status_id'     => $statusId,
            'create_date'                 => now()->toDateTimeString(),
            'modified_date'               => null,
        ]);
    }

    /**
     * Retorna a Ordem de Serviço com o status atual carregado.
     *
     * O status atual é determinado pelo último registro inserido no histórico
     * (service_order_has_service_order_status), ordenado por create_date.
     */
    public function findWithCurrentStatus(int $orderId): ?ServiceOrder
    {
        $order = ServiceOrder::with([
            'user',
            'vehicule.customer',
        ])->find($orderId);

        if (!$order) {
            return null;
        }

        $statusId = DB::table('service_order_has_service_order_status')
            ->where('service_order_id', $orderId)
            ->latest('create_date')
            ->value('service_order_status_id');

        $order->setRelation(
            'currentStatus',
            $statusId ? ServiceOrderStatus::find($statusId) : null
        );

        return $order;
    }

    /**
     * Retorna o histórico de mudanças de status da Ordem de Serviço.
     *
     * Os registros são ordenados pela data de criação para preservar a sequência
     * cronológica das transições de status, permitindo o cálculo do tempo médio
     * gasto entre cada etapa do fluxo da ordem.
     */
    public function getStatusHistory(int $orderId): Collect
    {
        return DB::table('service_order_has_service_order_status')
            ->where('service_order_id', $orderId)
            ->orderBy('create_date')
            ->get();
    }

    /**
     * Insere cada produto na tabela pivot, registrando o valor cobrado no momento
     * da inserção (snapshot do preço atual, preserva histórico de preços).
     */
    public function addProducts(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $products): void
    {
        foreach ($products as $product) {
            DB::table('service_order_has_products')->insertOrIgnore([
                'service_order_id'            => $orderId,
                'service_order_customer_id'   => $customerId,
                'service_order_users_id'      => $usersId,
                'service_order_users_role_id' => $usersRoleId,
                'products_id'                 => $product->id,
                'charged_value'               => $product->value,
            ]);
        }
    }

    public function addServices(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $services): void
    {
        foreach ($services as $service) {
            DB::table('service_order_has_services')->insertOrIgnore([
                'service_order_id'            => $orderId,
                'service_order_customer_id'   => $customerId,
                'service_order_users_id'      => $usersId,
                'service_order_users_role_id' => $usersRoleId,
                'services_id'                 => $service->id,
                'charged_value'               => $service->value,
            ]);
        }
    }

    /**
     * Calcula o valor total da ordem somando os charged_values de produtos e serviços.
     * Usa os valores registrados nas pivots (snapshot do momento da inserção),
     * garantindo que alterações futuras de preço não impactem ordens existentes.
     */
    public function calculateOrderTotal(int $orderId): float
    {
        $productsTotal = DB::table('service_order_has_products')
            ->where('service_order_id', $orderId)
            ->sum('charged_value');

        $servicesTotal = DB::table('service_order_has_services')
            ->where('service_order_id', $orderId)
            ->sum('charged_value');

        return (float) ($productsTotal + $servicesTotal);
    }

    /**
     * Atualiza o valor total da Ordem de Serviço.
     *
     * O valor informado deve corresponder ao total recalculado a partir dos
     * charged_values registrados nas tabelas de produtos e serviços, mantendo
     * o histórico de preços utilizados no momento da inserção dos itens.
     */
    public function updateOrderValue(int $orderId, float $value): bool
    {
        return (bool) ServiceOrder::where('id', $orderId)->update([
            'order_value'   => $value,
            'modified_date' => now()->toDateTimeString(),
        ]);
    }
}
