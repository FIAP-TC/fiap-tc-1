<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Vehicule\DTOs\VehiculeDTO;
use App\Application\Vehicule\UseCases\CreateVehiculeUseCase;
use App\Interfaces\Http\Requests\Vehicule\CreateVehiculeRequest;
use App\Interfaces\Http\Resources\VehiculeResource;
use Illuminate\Http\Response;

final class CreateVehiculeController
{
    /**
     * @api {post} /api/vehicules Create vehicule
     * @apiName CreateVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiBody {String} name Vehicule name.
     * @apiBody {String} plate License plate.
     * @apiBody {String} model Model.
     * @apiBody {String} brand Brand.
     * @apiBody {Number} years Year.
     * @apiBody {Number} customer_id Customer ID.
     */
    public function __invoke(
        CreateVehiculeRequest $request,
        CreateVehiculeUseCase $useCase,
    )
    {
        $vehicule = $useCase->execute(VehiculeDTO::fromArray($request->validated()));
        return VehiculeResource::make($vehicule)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}