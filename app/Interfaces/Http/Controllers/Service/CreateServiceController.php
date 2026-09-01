<?php

namespace App\Interfaces\Http\Controllers\Service;

use App\Application\Service\DTOs\ServiceDTO;
use App\Application\Service\UseCases\CreateServiceUseCase;
use App\Interfaces\Http\Requests\Service\CreateServiceRequest;
use App\Interfaces\Http\Resources\ServiceResource;
use Illuminate\Http\Response;

final class CreateServiceController
{
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
    public function __invoke(
        CreateServiceRequest $request,
        CreateServiceUseCase $useCase,
    ) {
        $serviceDTO = ServiceDTO::fromArray($request->validated());
        $service = $useCase->execute($serviceDTO);

        return ServiceResource::make($service)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
