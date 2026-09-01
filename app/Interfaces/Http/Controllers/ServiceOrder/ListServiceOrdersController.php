<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\ListServiceOrdersUseCase;
use App\Interfaces\Http\Resources\ServiceOrderResource;
use Illuminate\Http\JsonResponse;

final class ListServiceOrdersController
{
    /**
     * @api {get} /api/service-orders Listar Ordens de Serviço
     * @apiName ListServiceOrders
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        ListServiceOrdersUseCase $useCase,
    ): JsonResponse {
        $orders = $useCase->execute();
        return ServiceOrderResource::collection($orders)->response();
    }
}
