<?php

namespace App\Infrastructure\Persistence\Eloquent\ServiceOrder\Repositories;

use App\Domain\Product\Entites\ProductEntity;
use App\Domain\Service\Entites\ServiceEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\ServiceOrderMapper;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrder;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrderStatus;
use Illuminate\Support\Facades\DB;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    private const DETAIL_RELATIONS = ['vehicule.customer', 'products', 'services', 'statusHistory'];

    public function __construct(
        private readonly ServiceOrder $serviceOrderModel,
    ) {}

    public function findAll(): array
    {
        $models = $this->serviceOrderModel
            ->with(self::DETAIL_RELATIONS)
            ->where('status', true)
            ->get();

        return $models
            ->map(fn (ServiceOrder $model) => ServiceOrderMapper::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?ServiceOrderEntity
    {
        $model = $this->serviceOrderModel
            ->with(self::DETAIL_RELATIONS)
            ->where('status', true)
            ->find($id);

        return $model ? ServiceOrderMapper::toDomain($model) : null;
    }

    public function findByIdIgnoringStatus(int $id): ?ServiceOrderEntity
    {
        $model = $this->serviceOrderModel
            ->with(self::DETAIL_RELATIONS)
            ->find($id);

        return $model ? ServiceOrderMapper::toDomain($model) : null;
    }

    public function findWithCurrentStatus(int $orderId): ?ServiceOrderEntity
    {
        $model = $this->serviceOrderModel
            ->with(['vehicule.customer'])
            ->find($orderId);

        if (!$model) {
            return null;
        }

        $statusId = DB::table('service_order_has_service_order_status')
            ->where('service_order_id', $orderId)
            ->latest('create_date')
            ->value('service_order_status_id');

        $model->setRelation(
            'currentStatus',
            $statusId ? ServiceOrderStatus::find($statusId) : null
        );

        return ServiceOrderMapper::toDomain($model);
    }

    public function create(array $data): ServiceOrderEntity
    {
        $model = $this->serviceOrderModel->create($data);

        return ServiceOrderMapper::toDomain($model);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->serviceOrderModel->where('id', $id)->update($data);
    }

    /**
     * Soft-delete: mantém a ordem no banco com status=false,
     * preservando o histórico de status e os itens relacionados.
     */
    public function delete(int $id): bool
    {
        return (bool) $this->serviceOrderModel->where('id', $id)->update(['status' => false]);
    }

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

    public function addProducts(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $products): void
    {
        foreach ($products as $product) {
            /** @var ProductEntity $product */
            DB::table('service_order_has_products')->insertOrIgnore([
                'service_order_id'            => $orderId,
                'service_order_customer_id'   => $customerId,
                'service_order_users_id'      => $usersId,
                'service_order_users_role_id' => $usersRoleId,
                'products_id'                 => $product->getId(),
                'charged_value'               => $product->getValue(),
            ]);
        }
    }

    public function addServices(int $orderId, int $customerId, int $usersId, int $usersRoleId, array $services): void
    {
        foreach ($services as $service) {
            /** @var ServiceEntity $service */
            DB::table('service_order_has_services')->insertOrIgnore([
                'service_order_id'            => $orderId,
                'service_order_customer_id'   => $customerId,
                'service_order_users_id'      => $usersId,
                'service_order_users_role_id' => $usersRoleId,
                'services_id'                 => $service->getId(),
                'charged_value'               => $service->getValue(),
            ]);
        }
    }

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

    public function updateOrderValue(int $orderId, float $value): bool
    {
        return (bool) $this->serviceOrderModel->where('id', $orderId)->update([
            'order_value'   => $value,
            'modified_date' => now()->toDateTimeString(),
        ]);
    }
}
