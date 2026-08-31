<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\UseCases\ListUsersUseCase;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class ListUsersController
{
    /**
     * @api {get} /api/users Listar usuários
     * @apiName ListUsers
     * @apiGroup User
     * @apiHeader {String} Authorization Bearer {token}
     */
    public function __invoke(
        ListUsersUseCase $useCase,
    ): JsonResponse {
        $users = $useCase->execute();
        return UserResource::collection($users)->response();
    }
}
