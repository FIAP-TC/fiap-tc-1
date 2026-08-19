<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Application\Customer\UseCases\UpdateCustomerUseCase;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Interfaces\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;

final class UpdateCustomerController
{
    /**
     * @api {put} /api/customers/:id Update customer
     * @apiName UpdateCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function __invoke(
        int $id,
        UpdateCustomerUseCase $useCase, 
        UpdateCustomerRequest $request,
    ): JsonResponse
    {
        $customerDTO = CustomerDTO::fromArray($request->validated());
        $customers = $useCase->execute($id, $customerDTO);
        return CustomerResource::collection($customers)->response();
    }
}