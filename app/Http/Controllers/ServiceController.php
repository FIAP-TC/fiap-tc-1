<?php

namespace App\Http\Controllers;

use App\DTOs\Service\ServiceDTO;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Interfaces\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;

/**
 * Controller do módulo de Serviços da Mecânica.
 *
 * Responsabilidade única: receber a request → validar via FormRequest
 * → montar DTO → chamar ServiceService → retornar ServiceResource.
 * Nenhuma regra de negócio deve existir aqui.
 */
class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
    ) {}

    /**
     * @api {get} /api/services Listar todos os serviços
     * @apiName ListServices
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * { "data": [{ "id": 1, "name": "Troca de óleo", "value": 120.00, "status": true }] }
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ServiceResource::collection($this->serviceService->findAll()),
        ]);
    }

    /**
     * @api {get} /api/services/:id Buscar serviço por ID
     * @apiName GetService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID do serviço.
     *
     * @apiErrorExample {json} Not-Found:
     * HTTP/1.1 404 Not Found
     * { "message": "Serviço não encontrado." }
     */
    public function show(int $id): JsonResponse
    {
        $service = $this->serviceService->findById($id);

        if (!$service) {
            return $this->errorResponse('Serviço não encontrado.', 404);
        }

        return response()->json(['data' => new ServiceResource($service)]);
    }

    /**
     * @api {post} /api/services Criar serviço
     * @apiName CreateService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiBody {String} name Nome do serviço.
     * @apiBody {Number} value Valor do serviço (mínimo 0.01).
     * @apiBody {Boolean} [status] Status ativo/inativo (padrão: true).
     *
     * @apiSuccessExample {json} Created:
     * HTTP/1.1 201 Created
     * { "data": { "id": 1, "name": "Troca de óleo", "value": 120.00, "status": true } }
     */
    public function store(CreateServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->serviceService->create(ServiceDTO::fromArray($request->validated()));
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['data' => new ServiceResource($service)], 201);
    }

    /**
     * @api {put} /api/services/:id Atualizar serviço
     * @apiName UpdateService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID do serviço.
     * @apiBody {String} [name] Novo nome.
     * @apiBody {Number} [value] Novo valor.
     * @apiBody {Boolean} [status] Novo status (use true para reativar).
     */
    public function update(UpdateServiceRequest $request, int $id): JsonResponse
    {
        try {
            $service = $this->serviceService->update($id, ServiceDTO::fromArray($request->validated()));
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['data' => new ServiceResource($service)]);
    }

    /**
     * @api {delete} /api/services/:id Excluir serviço (soft-delete)
     * @apiName DeleteService
     * @apiGroup Service
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID do serviço.
     *
     * @apiSuccessExample {json} Deleted:
     * HTTP/1.1 200 OK
     * { "message": "Serviço excluído com sucesso." }
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->serviceService->delete($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['message' => 'Serviço excluído com sucesso.']);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
