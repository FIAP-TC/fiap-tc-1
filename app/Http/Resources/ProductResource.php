<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'type' => $product->type,
            'value' => $product->value,
            'quantity' => $product->quantity,
            'status' => (bool) $product->status,
            'created_at' => $product->create_date,
            'updated_at' => $product->modified_date,
        ];
    }
}
