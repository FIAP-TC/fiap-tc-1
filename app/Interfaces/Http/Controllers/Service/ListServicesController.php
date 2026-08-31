<?php

namespace App\Interfaces\Http\Controllers\Service;

use App\Application\Service\UseCases\ListServiceUseCase;
use App\Interfaces\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;

final class ListServicesController
{
    /**
     * @api {get} /api/services List services
     * @apiName ListServices
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        ListServiceUseCase $useCase,
    ): JsonResponse {
        $services = $useCase->execute();
        return ServiceResource::collection($services)->response();
    }
}
