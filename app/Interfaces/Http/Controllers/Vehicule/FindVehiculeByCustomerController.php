<?php

namespace App\Interfaces\Http\Controllers\Vehicule;

use App\Application\Customer\UseCases\FindCustomerUseCase;
use App\Application\Vehicule\UseCases\FindVehiculeByCustomerUseCase;
use App\Interfaces\Http\Resources\VehiculeResource;
use Illuminate\Http\JsonResponse;

final class FindVehiculeByCustomerController
{
     /**
     * @api {get} /api/customers/:customerId/vehicules List vehicules by customer
     * @apiName ListVehiculesByCustomer
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} customerId Customer ID.
     */
    public function __invoke(
        int $customerId,
        FindVehiculeByCustomerUseCase $useCase,
    ): JsonResponse
    {
        $vehicule = $useCase->execute($customerId);
        return VehiculeResource::collection($vehicule)->response();
    }
}   