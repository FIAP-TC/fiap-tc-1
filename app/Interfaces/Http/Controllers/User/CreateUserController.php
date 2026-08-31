<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\DTOs\UserDTO;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Interfaces\Http\Requests\User\CreateUserRequest;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class CreateUserController
{
    /**
     * @api {post} /api/users Criar usuário
     * @apiName CreateUser
     * @apiGroup User
     * @apiHeader {String} Authorization Bearer {token}
     *
     * @apiBody {String} username Username único.
     * @apiBody {String} password Senha (mínimo 6 caracteres).
     * @apiBody {Number} role_id ID da role.
     * @apiBody {Boolean} [status] Status ativo/inativo (padrão: true).
     */
    public function __invoke(
        CreateUserRequest $request,
        CreateUserUseCase $useCase,
    ): JsonResponse {
        $userDTO = UserDTO::fromArray($request->validated());
        $user = $useCase->execute($userDTO);

        return UserResource::make($user)->response()->setStatusCode(201);
    }
}
