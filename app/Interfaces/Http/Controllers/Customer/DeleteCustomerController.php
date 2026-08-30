<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Application\Customer\UseCases\DeleteCustomerUseCase;
use App\Application\Customer\UseCases\UpdateCustomerUseCase;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Interfaces\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class DeleteCustomerController
{
    /**
     * @api {delete} /api/customers/:id Delete customer
     * @apiName DeleteCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function __invoke(
        int $id,
        DeleteCustomerUseCase $useCase,
    ): JsonResponse {
        $useCase->execute($id);
        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
