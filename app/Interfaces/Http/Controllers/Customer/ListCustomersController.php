<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\UseCases\ListCustomersUseCases;
use App\Interfaces\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;

final class ListCustomersController
{
    /**
     * @api {get} /api/customers List all customers
     * @apiName ListCustomers
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
    */
    public function __invoke(
        ListCustomersUseCases $useCase, 
    ): JsonResponse
    {
        $customers = $useCase->execute();
        return CustomerResource::collection($customers)->response();
    }
}