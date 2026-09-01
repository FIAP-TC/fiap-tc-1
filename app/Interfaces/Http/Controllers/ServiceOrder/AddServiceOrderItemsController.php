<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\DTOs\ServiceOrderItemsDTO;
use App\Application\ServiceOrder\UseCases\AddServiceOrderItemsUseCase;
use App\Interfaces\Http\Requests\ServiceOrder\AddItemsRequest;
use App\Interfaces\Http\Resources\ServiceOrderResource;
use Illuminate\Http\JsonResponse;

final class AddServiceOrderItemsController
{
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
    public function __invoke(
        AddItemsRequest $request,
        int $id,
        AddServiceOrderItemsUseCase $useCase,
    ): JsonResponse {
        $order = $useCase->execute($id, ServiceOrderItemsDTO::fromArray($request->validated()));
        return ServiceOrderResource::make($order)->response();
    }
}
