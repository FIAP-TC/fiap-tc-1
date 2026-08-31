<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ServiceOrderEntity $resource
 */
class ServiceOrderTrackingResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ServiceOrderEntity $order */
        $order = $this->resource;

        $vehicule = $order->getVehicule();
        $customer = $vehicule?->getCustomer();
        $currentStatus = $order->getCurrentStatus();

        return [
            'id' => $order->getId(),
            'vehicle' => $vehicule ? [
                'id'    => $vehicule->getId(),
                'name'  => $vehicule->getName(),
                'plate' => $vehicule->getPlate(),
                'model' => $vehicule->getModel(),
                'brand' => $vehicule->getBrand(),
            ] : null,
            'customer' => $customer ? [
                'id'    => $customer->getId(),
                'name'  => $customer->getName(),
                'email' => $customer->getEmail(),
            ] : null,
            'order_value' => $order->getOrderValue(),
            'current_status' => $currentStatus ? [
                'id'   => $currentStatus->getId(),
                'name' => $currentStatus->getName(),
            ] : null,
        ];
    }
}
