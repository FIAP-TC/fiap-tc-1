<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Vehicule\DTOs\VehiculeDTO;
use App\Application\Vehicule\UseCases\UpdateVehiculeUseCase;
use App\Interfaces\Http\Requests\Vehicule\UpdateVehiculeRequest;
use App\Interfaces\Http\Resources\VehiculeResource;

final class UpdateVehiculeController
{
    /**
     * @api {put} /api/vehicules/:id Update vehicule
     * @apiName UpdateVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function __invoke(
        int $id,
        UpdateVehiculeRequest $request,
        UpdateVehiculeUseCase $useCase,
    )
    {
        $vehiculeDTO = VehiculeDTO::fromArray($request->validated());
        $vehicule = $useCase->execute($id, $vehiculeDTO);
        return VehiculeResource::make($vehicule)->response();
    }
}