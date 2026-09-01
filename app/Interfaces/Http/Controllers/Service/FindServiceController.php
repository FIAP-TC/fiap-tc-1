<?php

namespace App\Interfaces\Http\Controllers\Service;

use App\Application\Service\UseCases\FindServiceUseCase;
use App\Interfaces\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;

final class FindServiceController
{
    /**
     * @api {get} /api/services/:id Show service
     * @apiName ShowService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Service ID.
     */
    public function __invoke(
        int $id,
        FindServiceUseCase $useCase,
    ): JsonResponse {
        $service = $useCase->execute($id);
        return ServiceResource::make($service)->response();
    }
}
