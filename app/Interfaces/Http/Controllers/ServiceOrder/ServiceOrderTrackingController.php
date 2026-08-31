<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\GetServiceOrderTrackingUseCase;
use App\Interfaces\Http\Resources\ServiceOrderTrackingResource;
use Illuminate\Http\JsonResponse;

final class ServiceOrderTrackingController
{
    /**
     * @api {get} /api/service-orders/:orderId/track/status Status atual da ordem de serviço
     * @apiName GetServiceOrderTracking
     * @apiGroup ServiceOrder
     *
     * @apiParam {Number} orderId ID da Ordem de Serviço.
     */
    public function __invoke(
        int $orderId,
        GetServiceOrderTrackingUseCase $useCase,
    ): JsonResponse {
        $order = $useCase->execute($orderId);
        return ServiceOrderTrackingResource::make($order)->response();
    }
}
