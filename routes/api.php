<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Estrutura de autenticação:
|   - Rotas públicas: apenas POST /auth/login
|   - Rotas protegidas: middleware 'jwt' valida o token Bearer
|   - Rotas restritas por role: middleware 'admin' ou 'manager'
|
| Hierarquia de permissões:
|   Administrador > Gerente > Mecânico
|
*/

// Rota de teste (sem autenticação)
Route::prefix('test')->group(function () {
    Route::get('/', fn() => response()->json(['status' => 'success', 'mensagem' => 'Working']));
});

// -----------------------------------------------------------------------------
// Autenticação (rotas públicas)
// -----------------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    // Rotas protegidas por JWT
    Route::middleware('jwt')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// -----------------------------------------------------------------------------
// Recursos protegidos (requerem JWT válido)
// -----------------------------------------------------------------------------
Route::middleware('jwt')->group(function () {

    // CRUD de usuários — apenas Admin e Gerente podem gerenciar usuários
    Route::middleware('manager')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);

        // Criar e deletar usuários: apenas Administrador
        Route::middleware('admin')->group(function () {
            Route::post('/', [UserController::class, 'store']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });

        // Atualizar: Admin ou Gerente
        Route::put('/{id}', [UserController::class, 'update']);
    });
});
