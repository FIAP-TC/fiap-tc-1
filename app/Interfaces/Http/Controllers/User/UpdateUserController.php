<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\DTOs\UserDTO;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Interfaces\Http\Requests\User\UpdateUserRequest;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class UpdateUserController
{
    /**
     * @api {put} /api/users/:id Atualizar usuário
     * @apiName UpdateUser
     * @apiGroup User
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id User ID.
     *
     * @apiBody {String} [username] Novo username.
     * @apiBody {String} [password] Nova senha.
     * @apiBody {Number} [role_id] Nova role.
     * @apiBody {Boolean} [status] Novo status.
     */
    public function __invoke(
        UpdateUserRequest $request,
        int $id,
        UpdateUserUseCase $useCase,
    ): JsonResponse {
        $userDTO = UserDTO::fromArray($request->validated());
        $user = $useCase->execute($id, $userDTO);

        return UserResource::make($user)->response();
    }
}
