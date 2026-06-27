<?php

namespace App\Http\Controllers;

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

/**
 * Controller de usuários.
 *
 * Responsabilidade: receber request → validar via FormRequest →
 * montar DTO → chamar UserService → retornar UserResource.
 *
 * Nenhuma regra de negócio aqui. Qualquer lógica que aparecer
 * deve ser movida para o UserService.
 */
class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * @api {get} /api/users Listar usuários
     *
     * @apiGroup Users
     *
     * @apiName GetUsers
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": [
     *         { "id": 1, "username": "admin", "status": true, "role": { "id": 1, "name": "Administrador" } }
     *     ]
     * }
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->listAll();

        return response()->json([
            'success' => true,
            'errors'  => [],
            'data'    => UserResource::collection($users),
        ]);
    }

    /**
     * @api {get} /api/users/:id Buscar usuário por ID
     *
     * @apiGroup Users
     *
     * @apiName GetUser
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiParam {Number} id ID do usuário.
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": { "id": 1, "username": "admin", "status": true, "role": { "id": 1, "name": "Administrador" } }
     * }
     *
     * @apiErrorExample {json} Not-Found:
     * HTTP/1.1 404 Not Found
     * { "success": false, "errors": ["Usuário #99 não encontrado."], "data": [] }
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->findById($id);

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => new UserResource($user),
            ]);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * @api {post} /api/users Criar usuário
     *
     * @apiGroup Users
     *
     * @apiName CreateUser
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiBody {String} username  Username único.
     * @apiBody {String} password  Senha (mínimo 6 caracteres).
     * @apiBody {Number} role_id   ID da role.
     * @apiBody {Boolean} [status] Status ativo/inativo (padrão: true).
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 201 Created
     * {
     *     "success": true,
     *     "errors": [],
     *     "data": { "id": 4, "username": "novo", "status": true, "role": { "id": 2, "name": "Gerente" } }
     * }
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create(CreateUserDTO::fromArray($request->validated()));

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => new UserResource($user),
            ], 201);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * @api {put} /api/users/:id Atualizar usuário
     *
     * @apiGroup Users
     *
     * @apiName UpdateUser
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiParam {Number} id ID do usuário.
     *
     * @apiBody {String}  [username] Novo username.
     * @apiBody {String}  [password] Nova senha.
     * @apiBody {Number}  [role_id]  Nova role.
     * @apiBody {Boolean} [status]   Novo status.
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->update($id, UpdateUserDTO::fromArray($request->validated()));

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => new UserResource($user),
            ]);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * @api {delete} /api/users/:id Excluir usuário
     *
     * @apiGroup Users
     *
     * @apiName DeleteUser
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization Bearer token. Uso: `Bearer <token>`
     *
     * @apiParam {Number} id ID do usuário.
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * { "success": true, "errors": [], "data": { "message": "Usuário removido com sucesso." } }
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);

            return response()->json([
                'success' => true,
                'errors'  => [],
                'data'    => ['message' => 'Usuário removido com sucesso.'],
            ]);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(\RuntimeException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors'  => [$e->getMessage()],
            'data'    => [],
        ], $e->getCode() ?: 500);
    }
}
