<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Service $service */
        $service = $this->resource;

        return [
            'id'         => $service->id,
            'name'       => $service->name,
            'value'      => $service->value,
            'status'     => (bool) $service->status,
            'created_at' => $service->create_date,
            'updated_at' => $service->modified_date,
        ];
    }
}
