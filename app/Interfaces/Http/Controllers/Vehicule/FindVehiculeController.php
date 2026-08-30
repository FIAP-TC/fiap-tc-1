<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Vehicule\UseCases\FindVehiculeUseCase;
use App\Interfaces\Http\Resources\VehiculeResource;
use Illuminate\Http\JsonResponse;

final class FindVehiculeController
{
    /**
     * @api {get} /api/vehicules/:id Get vehicule
     * @apiName GetVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function __invoke(
        int $id,
        FindVehiculeUseCase $useCase,
    ): JsonResponse
    {
        $vehicule = $useCase->execute($id);
        return VehiculeResource::make($vehicule)->response();
    }
}