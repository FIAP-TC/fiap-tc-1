<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\FindServiceOrderUseCase;
use App\Interfaces\Http\Resources\ServiceOrderResource;
use Illuminate\Http\JsonResponse;

final class FindServiceOrderController
{
    /**
     * @api {get} /api/service-orders/:id Buscar Ordem de Serviço por ID
     * @apiName GetServiceOrder
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID da Ordem de Serviço.
     */
    public function __invoke(
        int $id,
        FindServiceOrderUseCase $useCase,
    ): JsonResponse {
        $order = $useCase->execute($id);
        return ServiceOrderResource::make($order)->response();
    }
}
