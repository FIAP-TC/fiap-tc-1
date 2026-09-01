<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderItemEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderStatusHistoryEntryEntity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ServiceOrderEntity $resource
 */
class ServiceOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ServiceOrderEntity $order */
        $order = $this->resource;

        return [
            'id'            => $order->getId(),
            'users_id'      => $order->getUsersId(),
            'users_role_id' => $order->getUsersRoleId(),
            'order_value'   => $order->getOrderValue(),
            'time_average'  => $order->getTimeAverage(),
            'status'        => $order->isActive(),
            'created_at'    => $order->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'    => $order->getModifiedDate()?->format('Y-m-d H:i:s'),
            'vehicule'      => $order->getVehicule() ? VehiculeResource::make($order->getVehicule()) : null,
            'products'      => array_map(fn (ServiceOrderItemEntity $item) => [
                'id'            => $item->getId(),
                'name'          => $item->getName(),
                'type'          => $item->getType(),
                'charged_value' => $item->getChargedValue(),
            ], $order->getProducts()),
            'services'      => array_map(fn (ServiceOrderItemEntity $item) => [
                'id'            => $item->getId(),
                'name'          => $item->getName(),
                'charged_value' => $item->getChargedValue(),
            ], $order->getServices()),
            'status_history' => array_map(fn (ServiceOrderStatusHistoryEntryEntity $entry) => [
                'id'         => $entry->getStatus()->getId(),
                'name'       => $entry->getStatus()->getName(),
                'create_date' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
            ], $order->getStatusHistory()),
        ];
    }
}
