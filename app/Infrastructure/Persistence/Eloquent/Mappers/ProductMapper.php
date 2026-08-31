<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Product\Entites\ProductEntity;
use App\Infrastructure\Persistence\Eloquent\Product\Models\Product;

class ProductMapper
{
    public static function toDomain(Product $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            name: $model->name,
            type: $model->type,
            value: (float) $model->value,
            quantity: (int) $model->quantity,
            status: (bool) $model->status,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}