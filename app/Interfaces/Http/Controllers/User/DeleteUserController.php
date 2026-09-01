<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\UseCases\DeleteUserUseCase;
use Illuminate\Http\JsonResponse;

final class DeleteUserController
{
    /**
     * @api {delete} /api/users/:id Excluir usuário
     * @apiName DeleteUser
     * @apiGroup User
     * @apiHeader {String} Authorization Bearer {token}
     * @apiParam {Number} id User ID.
     */
    public function __invoke(
        int $id,
        DeleteUserUseCase $useCase,
    ): JsonResponse {
        $useCase->execute($id);
        return response()->json(['message' => 'Usuário removido com sucesso.']);
    }
}
