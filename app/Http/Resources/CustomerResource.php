<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Customer $customer */
        $customer = $this->resource;

        return [
            'id'                    => $customer->id,
            'name'                  => $customer->name,
            'identification'        => $customer->identification,
            'identification_number' => $customer->identification_number,
            'email'                 => $customer->email,
            'status'                => (bool) $customer->status,
            'created_at'            => $customer->create_date,
            'updated_at'            => $customer->modified_date,
            'vehicules'             => VehiculeResource::collection($this->whenLoaded('vehicules')),
        ];
    }
}
