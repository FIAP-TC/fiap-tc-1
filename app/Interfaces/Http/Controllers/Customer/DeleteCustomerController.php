<?php

namespace App\Interfaces\Http\Controllers\Customer;

use App\Application\Customer\UseCases\DeleteCustomerUseCase;
use Illuminate\Http\JsonResponse;

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
        return response()->json([
            'message' => 'Customer deleted successfully.'
        ]);
    }
}
