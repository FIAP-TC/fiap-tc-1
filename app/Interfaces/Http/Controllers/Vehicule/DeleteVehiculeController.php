<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Vehicule\UseCases\DeleteVehiculeUseCase;
use Illuminate\Http\JsonResponse;

final class DeleteVehiculeController
{
    /**
     * @api {delete} /api/vehicules/:id Delete vehicule
     * @apiName DeleteVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function __invoke(
        int $id,
        DeleteVehiculeUseCase $useCase,
    ): JsonResponse
    {
        $useCase->execute($id);
        return response()->json(['message' => 'Vehicule deleted successfully.']);
    }
}   