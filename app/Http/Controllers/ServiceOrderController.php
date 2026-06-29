<?php

namespace App\Http\Controllers;

use App\DTOs\OrderService\UpdateServiceOrderStatusDTO;
use App\Http\Requests\OrderService\UpdateServiceOrderStatusRequest;
use App\DTOs\ServiceOrder\ServiceOrderDTO;
use App\DTOs\ServiceOrder\ServiceOrderItemsDTO;
use App\Http\Requests\ServiceOrder\AddItemsRequest;
use App\Http\Requests\ServiceOrder\CreateServiceOrderRequest;
use App\Http\Resources\ServiceOrderResource;
use App\Services\ServiceOrderService;
use Illuminate\Http\JsonResponse;

/**
 * Controller da Ordem de Serviço.
 *
 * Responsabilidade única: receber a request → extrair dados (incluindo
 * usuário autenticado via JWT) → montar DTO → delegar ao ServiceOrderService.
 * Nenhuma regra de negócio aqui.
 */
class ServiceOrderController extends Controller
{
    public function __construct(
        private readonly ServiceOrderService $serviceOrderService,
    ) {}

    /**
     * @api {get} /api/service-orders Listar Ordens de Serviço
     * @apiName ListServiceOrders
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ServiceOrderResource::collection($this->serviceOrderService->findAll()),
        ]);
    }

    /**
     * @api {get} /api/service-orders/:id Buscar Ordem de Serviço por ID
     * @apiName GetServiceOrder
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID da Ordem de Serviço.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->serviceOrderService->findById($id);

        if (!$order) {
            return $this->errorResponse('Ordem de Serviço não encontrada.', 404);
        }

        return response()->json(['data' => new ServiceOrderResource($order)]);
    }

    /**
     * @api {post} /api/service-orders Criar Ordem de Serviço
     * @apiName CreateServiceOrder
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiBody {Number} vehicules_id ID do veículo.
     * @apiBody {Number} [time_average] Tempo médio estimado.
     *
     * O usuário responsável é extraído automaticamente do token JWT —
     * não é necessário enviar users_id no body.
     */
    public function store(CreateServiceOrderRequest $request): JsonResponse
    {
        try {
            $dto = ServiceOrderDTO::fromArray(array_merge($request->validated(), [
                'users_id'      => auth()->id(),
                'users_role_id' => auth()->user() instanceof \App\Models\User ? auth()->user()->role_id : null,
            ]));

            $order = $this->serviceOrderService->create($dto);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['data' => new ServiceOrderResource($order)], 201);
    }

    /**
     * @api {delete} /api/service-orders/:id Excluir Ordem de Serviço (Soft Delete)
     * @apiName DeleteServiceOrder
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID da Ordem de Serviço.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->serviceOrderService->delete($id);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['message' => 'Ordem de Serviço excluída com sucesso.']);
    }

    /**
     * @api {post} /api/service-orders/:id/items Adicionar Produtos e Serviços
     * @apiName AddServiceOrderItems
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number}   id       ID da Ordem de Serviço.
     * @apiBody {Number[]}  [products] Lista de IDs de produtos.
     * @apiBody {Number[]}  [services] Lista de IDs de serviços.
     *
     * Após a inserção, o order_value é recalculado automaticamente.
     */
    public function addItems(AddItemsRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->serviceOrderService->addItems(
                $id,
                ServiceOrderItemsDTO::fromArray($request->validated()),
            );
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }

        return response()->json(['data' => new ServiceOrderResource($order)]);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['message' => $message], $status);
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
