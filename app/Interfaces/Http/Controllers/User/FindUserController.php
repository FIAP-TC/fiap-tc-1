<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\UseCases\FindUserUseCase;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class FindUserController
{
    /**
     * @api {get} /api/users/:id Buscar usuário por ID
     * @apiName ShowUser
     * @apiGroup User
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id User ID.
     */
    public function __invoke(
        int $id,
        FindUserUseCase $useCase,
    ): JsonResponse {
        $user = $useCase->execute($id);
        return UserResource::make($user)->response();
    }
}
