<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderItemEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderStatusEntity;
use App\Domain\ServiceOrder\Entites\ServiceOrderStatusHistoryEntryEntity;
use App\Infrastructure\Persistence\Eloquent\Product\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrder;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrderStatus;
use DateTimeImmutable;

class ServiceOrderMapper
{
    public static function toDomain(ServiceOrder $model): ServiceOrderEntity
    {
        $vehicule = null;
        if ($model->relationLoaded('vehicule') && $model->vehicule) {
            $vehicule = VehiculeMapper::toDomain($model->vehicule);
        }

        $products = [];
        if ($model->relationLoaded('products')) {
            $products = $model->products
                ->map(fn (Product $product) => new ServiceOrderItemEntity(
                    id: $product->id,
                    name: $product->name,
                    chargedValue: (float) $product->pivot->charged_value,
                    type: $product->type,
                ))
                ->all();
        }

        $services = [];
        if ($model->relationLoaded('services')) {
            $services = $model->services
                ->map(fn (Service $service) => new ServiceOrderItemEntity(
                    id: $service->id,
                    name: $service->name,
                    chargedValue: (float) $service->pivot->charged_value,
                ))
                ->all();
        }

        $statusHistory = [];
        if ($model->relationLoaded('statusHistory')) {
            $statusHistory = $model->statusHistory
                ->map(fn (ServiceOrderStatus $status) => new ServiceOrderStatusHistoryEntryEntity(
                    status: new ServiceOrderStatusEntity(id: $status->id, name: $status->name),
                    createdAt: new DateTimeImmutable($status->pivot->create_date),
                ))
                ->all();
        }

        $currentStatus = null;
        if ($model->relationLoaded('currentStatus') && $model->currentStatus) {
            $currentStatus = new ServiceOrderStatusEntity(
                id: $model->currentStatus->id,
                name: $model->currentStatus->name,
            );
        }

        return new ServiceOrderEntity(
            id: $model->id,
            usersId: (int) $model->users_id,
            usersRoleId: (int) $model->users_role_id,
            vehiculesId: (int) $model->vehicules_id,
            orderValue: (float) $model->order_value,
            timeAverage: $model->time_average !== null ? (float) $model->time_average : null,
            status: (bool) $model->status,
            vehicule: $vehicule,
            products: $products,
            services: $services,
            statusHistory: $statusHistory,
            currentStatus: $currentStatus,
            createdAt: $model->create_date,
            modifiedDate: $model->modified_date,
        );
    }
}
