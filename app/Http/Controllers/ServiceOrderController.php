<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ServiceOrderController extends Controller
{
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
}
