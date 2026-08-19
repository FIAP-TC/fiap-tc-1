<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\UseCases\FindCustomerUseCase;
use App\Interfaces\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;

final class FindCustomerController
{
    /**
     * @api {get} /api/customers/:id Get customer
     * @apiName GetCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function __invoke(
        int $id,
        FindCustomerUseCase $useCase,
    ): JsonResponse
    {
        $customer = $useCase->execute($id);
        return CustomerResource::make($customer)->response();
    }
}   