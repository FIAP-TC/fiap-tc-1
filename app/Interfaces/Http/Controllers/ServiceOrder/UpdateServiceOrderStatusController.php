<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\DTOs\UpdateServiceOrderStatusDTO;
use App\Application\ServiceOrder\UseCases\UpdateServiceOrderStatusUseCase;
use App\Interfaces\Http\Requests\ServiceOrder\UpdateServiceOrderStatusRequest;
use Illuminate\Http\JsonResponse;

final class UpdateServiceOrderStatusController
{
    /**
     * @api {patch} /api/service-orders/:id/status Update service order status
     * @apiName UpdateServiceOrderStatus
     * @apiGroup ServiceOrder
     * @apiHeader {String} Authorization Bearer {token}
     *
     * @apiParam {Number} id Service Order ID.
     * @apiBody {Number} status_id Service order status id.
     */
    public function __invoke(
        UpdateServiceOrderStatusRequest $request,
        int $id,
        UpdateServiceOrderStatusUseCase $useCase,
    ): JsonResponse {
        $dto = UpdateServiceOrderStatusDTO::fromArray($request->validated());
        $useCase->execute($id, $dto);

        return response()->json([
            'message' => 'Status da ordem de serviço atualizado com sucesso.',
        ]);
    }
}
