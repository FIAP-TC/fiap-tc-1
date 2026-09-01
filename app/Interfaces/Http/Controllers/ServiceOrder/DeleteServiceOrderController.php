<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\DeleteServiceOrderUseCase;
use Illuminate\Http\JsonResponse;

final class DeleteServiceOrderController
{
    /**
     * @api {delete} /api/service-orders/:id Excluir Ordem de Serviço (Soft Delete)
     * @apiName DeleteServiceOrder
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id ID da Ordem de Serviço.
     */
    public function __invoke(
        int $id,
        DeleteServiceOrderUseCase $useCase,
    ): JsonResponse {
        $useCase->execute($id);
        return response()->json(['message' => 'Ordem de Serviço excluída com sucesso.']);
    }
}
