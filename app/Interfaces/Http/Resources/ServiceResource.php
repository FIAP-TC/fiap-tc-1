<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\Service\Entites\ServiceEntity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ServiceEntity $resource
 */
class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ServiceEntity $service */
        $service = $this->resource;

        return [
            'id'         => $service->getId(),
            'name'       => $service->getName(),
            'value'      => $service->getValue(),
            'status'     => $service->isActive(),
            'created_at' => $service->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $service->getModifiedDate()?->format('Y-m-d H:i:s'),
        ];
    }
}
