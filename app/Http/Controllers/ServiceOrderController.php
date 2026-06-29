<?php

namespace App\Http\Controllers;

use App\DTOs\OrderService\UpdateServiceOrderStatusDTO;
use App\Http\Requests\OrderService\UpdateServiceOrderStatusRequest;
use App\Services\ServiceOrderService;
use Illuminate\Http\JsonResponse;

class ServiceOrderController extends Controller
{
    public function __construct(
        private ServiceOrderService $serviceOrderService,    
    ){}

    /**
     * @api {get} /v1/service-orders Lista ordens de serviço
     *
     * @apiGroup ServiceOrder
     *
     * @apiName GetServiceOrders
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Token de autenticação. Uso: `Bearer <token>`
     *
     * @apiSuccess {Boolean} success Status da requisição.
     * @apiSuccess {Array}   errors  Lista de erros (vazia em caso de sucesso).
     * @apiSuccess {Array}   data    Lista de ordens de serviço.
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": [
     *         {
     *             "id": 1,
     *             "status": "PENDENTE",
     *             "order_value": "350.00",
     *             "create_date": "2026-06-24 10:00:00"
     *         }
     *     ]
     * }
     *
     * @apiErrorExample {json} Unauthorized:
     * HTTP/1.1 401 Unauthorized
     * {
     *     "success": false,
     *     "errors": ["Não autenticado"],
     *     "data": []
     * }
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'errors'  => [],
            'data'    => [],
        ]);
    }

    /**
     * @api {post} /v1/service-orders Criar ordem de serviço
     *
     * @apiGroup ServiceOrder
     *
     * @apiName CreateServiceOrder
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Token de autenticação. Uso: `Bearer <token>`
     *
     * @apiBody {Number}  users_id              ID do usuário responsável.
     * @apiBody {Number}  vehicules_id          ID do veículo.
     * @apiBody {Number}  vehicules_customer_id ID do cliente do veículo.
     * @apiBody {Decimal} order_value           Valor total da ordem.
     * @apiBody {String}  status                Status: APROVADO | PENDENTE | NEGADO.
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 201 Created
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": {
     *         "id": 1,
     *         "status": "PENDENTE",
     *         "order_value": "350.00"
     *     }
     * }
     *
     * @apiErrorExample {json} Validation-Error:
     * HTTP/1.1 422 Unprocessable Entity
     * {
     *     "success": false,
     *     "errors": ["O campo order_value é obrigatório"],
     *     "data": []
     * }
     */
    public function store(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'errors'  => [],
            'data'    => [],
        ], 201);
    }

    /**
     * @api {patch} /api/service-orders/:id/status Update service order status
     * @apiName UpdateServiceOrderStatus
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     *
     * @apiParam {Number} id Service Order ID.
     *
     * @apiBody {Number} status_id Service order status id.
     */
    public function updateStatus(UpdateServiceOrderStatusRequest $request, int $id): JsonResponse 
    {
        try {
            $dto = UpdateServiceOrderStatusDTO::fromArray($request->validated());
            $this->serviceOrderService->updateStatus($id, $dto);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return response()->json([
            'message' => 'Service order status updated successfully.',
        ]);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
