<?php

namespace App\Http\Controllers;

use App\DTOs\Vehicule\CreateVehiculeDTO;
use App\DTOs\Vehicule\UpdateVehiculeDTO;
use App\Http\Requests\Vehicule\CreateVehiculeRequest;
use App\Http\Requests\Vehicule\UpdateVehiculeRequest;
use App\Http\Resources\VehiculeResource;
use App\Services\VehiculeService;
use Illuminate\Http\JsonResponse;

class VehiculeController extends Controller
{
    public function __construct(
        private readonly VehiculeService $vehiculeService,
    ) {}

    /**
     * @api {get} /api/vehicules List all vehicules
     * @apiName ListVehicules
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function index(): JsonResponse
    {
        $vehicules = $this->vehiculeService->findAll();

        return response()->json([
            'data' => VehiculeResource::collection($vehicules),
        ]);
    }

    /**
     * @api {get} /api/vehicules/:id Get vehicule
     * @apiName GetVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function show(int $id): JsonResponse
    {
        $vehicule = $this->vehiculeService->findById($id);

        if (!$vehicule) {
            return $this->errorResponse('Vehicule not found.', 404);
        }

        return response()->json(['data' => new VehiculeResource($vehicule)]);
    }

    /**
     * @api {get} /api/customers/:customerId/vehicules List vehicules by customer
     * @apiName ListVehiculesByCustomer
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} customerId Customer ID.
     */
    public function byCustomer(int $customerId): JsonResponse
    {
        try {
            $vehicules = $this->vehiculeService->findByCustomer($customerId);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['data' => VehiculeResource::collection($vehicules)]);
    }

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
    public function store(CreateVehiculeRequest $request): JsonResponse
    {
        try {
            $vehicule = $this->vehiculeService->create(
                CreateVehiculeDTO::fromArray($request->validated())
            );
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['data' => new VehiculeResource($vehicule)], 201);
    }

    /**
     * @api {put} /api/vehicules/:id Update vehicule
     * @apiName UpdateVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function update(UpdateVehiculeRequest $request, int $id): JsonResponse
    {
        try {
            $vehicule = $this->vehiculeService->update($id, UpdateVehiculeDTO::fromArray($request->validated()));
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['data' => new VehiculeResource($vehicule)]);
    }

    /**
     * @api {delete} /api/vehicules/:id Delete vehicule
     * @apiName DeleteVehicule
     * @apiGroup Vehicule
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id Vehicule ID.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->vehiculeService->delete($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json(['message' => 'Vehicule deleted successfully.']);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
