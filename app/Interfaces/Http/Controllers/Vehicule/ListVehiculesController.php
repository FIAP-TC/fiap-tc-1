<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Vehicule\UseCases\ListVehiculesUseCase;
use App\Interfaces\Http\Resources\VehiculeResource;

final class ListVehiculesController
{
    /**
     * @api {get} /api/vehicules List all vehicules
     * @apiName ListVehicules
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        ListVehiculesUseCase $useCase,
    )
    {
        $vehicule = $useCase->execute();
        return VehiculeResource::collection($vehicule)->response();
    }
}