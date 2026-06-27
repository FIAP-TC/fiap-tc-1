<?php

namespace App\Http\Controllers;

use App\DTOs\Customer\CustomerDTO;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    /**
     * @api {get} /api/customers List all customers
     * @apiName ListCustomers
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => CustomerResource::collection($this->customerService->findAll()),
        ]);
    }

    /**
     * @api {get} /api/customers/:id Get customer
     * @apiName GetCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (!$customer) {
            return $this->errorResponse('Customer not found.', 404);
        }

        return response()->json(['data' => new CustomerResource($customer)]);
    }

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
    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create(CustomerDTO::fromArray($request->validated()));

        return response()->json(['data' => new CustomerResource($customer)], 201);
    }

    /**
     * @api {put} /api/customers/:id Update customer
     * @apiName UpdateCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function update(UpdateCustomerRequest $request, int $id): JsonResponse
    {
        try {
            $customer = $this->customerService->update($id, CustomerDTO::fromArray($request->validated()));
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['data' => new CustomerResource($customer)]);
    }

    /**
     * @api {delete} /api/customers/:id Delete customer
     * @apiName DeleteCustomer
     * @apiGroup Customer
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Customer ID.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->customerService->delete($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['message' => 'Customer deleted successfully.']);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
