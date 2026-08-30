<?php

namespace App\Interfaces\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Customer\Entites\CustomerEntity;

/**
 * @property-read CustomerEntity $resource
 */
class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var CustomerEntity $customer */
        $customer = $this->resource;

        return [
            'id'                    => $customer->getId(),
            'name'                  => $customer->getName(),
            'identification'        => $customer->getIdentification(),
            'identification_number' => $customer->getIdentificationNumber(),
            'email'                 => $customer->getEmail(),
            'status'                => $customer->isActive(),
            'created_at'            => $customer->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'            => $customer->getModifiedDate()?->format('Y-m-d H:i:s'),
            'vehicules'             => VehiculeResource::collection($customer->getVehicles() ?? []),
        ];
    }
}
