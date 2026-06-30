<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceOrderTrackingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => [
                'id'    => $this->vehicule->id,
                'name'  => $this->vehicule->name,
                'plate' => $this->vehicule->plate,
                'model' => $this->vehicule->model,
                'brand' => $this->vehicule->brand,
            ],
            'customer' => [
                'id'    => $this->vehicule->customer->id,
                'name'  => $this->vehicule->customer->name,
                'email' => $this->vehicule->customer->email,
            ],
            'order_value' => $this->order_value,
            'current_status' => $this->currentStatus
                ? [
                    'id'   => $this->currentStatus->id,
                    'name' => $this->currentStatus->name,
                ]
                : null,
        ];
    }
}
