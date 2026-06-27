<?php

namespace App\Http\Resources;

use App\Models\Vehicule;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vehicule
 */
class VehiculeResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Vehicule $vehicule */
        $vehicule = $this->resource;

        return [
            'id'          => $vehicule->id,
            'name'        => $vehicule->name,
            'plate'       => $vehicule->plate,
            'model'       => $vehicule->model,
            'brand'       => $vehicule->brand,
            'years'       => $vehicule->years,
            'status'      => (bool) $vehicule->status,
            'customer_id' => $vehicule->customer_id,
            'created_at'  => $vehicule->create_date,
            'updated_at'  => $vehicule->modified_date,
            'customer'    => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
