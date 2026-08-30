<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\Product\Entities\ProductEntity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ProductEntity $resource
 */
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ProductEntity $product */
        $product = $this->resource;

        return [
            'id'            => $product->getId(),
            'name'          => $product->getName(),
            'type'          => $product->getType(),
            'value'         => $product->getValue(),
            'quantity'      => $product->getQuantity(),
            'status'        => $product->isActive(),
            'created_at'    => $product->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'    => $product->getModifiedDate()?->format('Y-m-d H:i:s'),
        ];
    }
}