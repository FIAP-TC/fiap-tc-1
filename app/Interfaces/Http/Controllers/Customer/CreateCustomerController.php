<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Application\Customer\UseCases\CreateCustomerUseCase;
use App\Interfaces\Http\Requests\Customer\CreateCustomerRequest;
use App\Interfaces\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CreateCustomerController
{
    /**
     * @api {post} /api/customers Create customer
     * @apiName CreateCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiBody {String} name Customer name.
     * @apiBody {String} identification CPF or CNPJ.
     * @apiBody {Number} identification_number Identification number.
     * @apiBody {String} email Customer email.
     */
    public function __invoke(
        CreateCustomerRequest $request,
        CreateCustomerUseCase $useCase
    ): JsonResponse
    {
        $customer = $useCase->execute(CustomerDTO::fromArray($request->validated()));
        return CustomerResource::make($customer)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}