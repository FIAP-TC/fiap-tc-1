<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\DTOs\ServiceOrderDTO;
use App\Application\ServiceOrder\UseCases\CreateServiceOrderUseCase;
use App\Interfaces\Http\Requests\ServiceOrder\CreateServiceOrderRequest;
use App\Interfaces\Http\Resources\ServiceOrderResource;
use App\Infrastructure\Persistence\Eloquent\User\Models\User;
use Illuminate\Http\JsonResponse;

final class CreateServiceOrderController
{
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
    public function __invoke(
        CreateServiceOrderRequest $request,
        CreateServiceOrderUseCase $useCase,
    ): JsonResponse {
        $dto = ServiceOrderDTO::fromArray(array_merge($request->validated(), [
            'users_id'      => auth()->id(),
            'users_role_id' => auth()->user() instanceof User ? auth()->user()->role_id : null,
        ]));

        $order = $useCase->execute($dto);

        return ServiceOrderResource::make($order)->response()->setStatusCode(201);
    }
}
