<?php

namespace App\Interfaces\Http\Controllers\Service;

use App\Application\Service\DTOs\ServiceDTO;
use App\Application\Service\UseCases\UpdateServiceUseCase;
use App\Interfaces\Http\Requests\Service\UpdateServiceRequest;
use App\Interfaces\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;

final class UpdateServiceController
{
    /**
     * @api {put} /api/services/:id Update service
     * @apiName UpdateService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Service ID.
     *
     * @apiBody {String} [name] Service name.
     * @apiBody {Number} [value] Service value.
     * @apiBody {Boolean} [status] Service status.
     */
    public function __invoke(
        UpdateServiceRequest $request,
        int $id,
        UpdateServiceUseCase $useCase,
    ): JsonResponse {
        $serviceDTO = ServiceDTO::fromArray($request->validated());
        $service = $useCase->execute($id, $serviceDTO);
        return ServiceResource::make($service)->response();
    }
}
