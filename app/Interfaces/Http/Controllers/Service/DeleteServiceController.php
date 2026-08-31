<?php

namespace App\Interfaces\Http\Controllers\Service;

use App\Application\Service\UseCases\DeleteServiceUseCase;
use Illuminate\Http\JsonResponse;

final class DeleteServiceController
{
    /**
     * @api {delete} /api/services/:id Delete service
     * @apiName DeleteService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Service ID.
     */
    public function __invoke(
        int $id,
        DeleteServiceUseCase $useCase,
    ): JsonResponse {
        $useCase->execute($id);
        return response()->json(['message' => 'Service deleted successfully.']);
    }
}
