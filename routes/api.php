<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ServiceOrderApprovalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
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

Route::prefix('service-orders')->group(function () {
    Route::get('/approve', [ServiceOrderApprovalController::class, 'approve']);
    Route::get('/reject', [ServiceOrderApprovalController::class, 'reject']);
    Route::get('/{orderId}/track/status', [ServiceOrderController::class, 'showCurrentStatus']);
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
    });

    Route::post('refresh', [AuthController::class, 'refresh']);
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

    // CRUD de clientes — apenas Admin e Gerente
    Route::middleware('manager')->prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);

        Route::middleware('admin')->group(function () {
            Route::post('/', [CustomerController::class, 'store']);
            Route::delete('/{id}', [CustomerController::class, 'destroy']);
        });
    });

    // Listagem de veículos por cliente
    Route::middleware('manager')->get(
        '/customers/{customerId}/vehicules',
        [VehiculeController::class, 'byCustomer']
    );

    // CRUD de veículos — apenas Admin e Gerente
    Route::middleware('manager')->prefix('vehicules')->group(function () {
        Route::get('/', [VehiculeController::class, 'index']);
        Route::get('/{id}', [VehiculeController::class, 'show']);
        Route::put('/{id}', [VehiculeController::class, 'update']);

        Route::middleware('admin')->group(function () {
            Route::post('/', [VehiculeController::class, 'store']);
            Route::delete('/{id}', [VehiculeController::class, 'destroy']);
        });
    });

    // Ordens de Serviço — Admin e Gerente listam/buscam/excluem; todos criam
    Route::middleware('manager')->prefix('service-orders')->group(function () {
        Route::get('/', [ServiceOrderController::class, 'index']);
        Route::get('/{id}', [ServiceOrderController::class, 'show']);
        Route::post('/', [ServiceOrderController::class, 'store']);
        Route::post('/{id}/items', [ServiceOrderController::class, 'addItems']);

        Route::patch('/{id}/status', [ServiceOrderController::class, 'updateStatus']);

        Route::middleware('admin')->group(function () {
            Route::delete('/{id}', [ServiceOrderController::class, 'destroy']);
        });
    });

    // CRUD de serviços da mecânica — apenas Admin e Gerente
    Route::middleware('manager')->prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/{id}', [ServiceController::class, 'show']);
        Route::put('/{id}', [ServiceController::class, 'update']);

        // Criar e excluir: apenas Administrador
        Route::middleware('admin')->group(function () {
            Route::post('/', [ServiceController::class, 'store']);
            Route::delete('/{id}', [ServiceController::class, 'destroy']);
        });
    });
    
    // CRUD de Produtos — apenas Admin e Gerente
    Route::middleware('manager')->prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'index']);
        Route::get('/{id}', [ProductsController::class, 'show']);
        Route::put('/{id}', [ProductsController::class, 'update']);

        // Atualização de estoque
        Route::patch('/{id}/increase-stock', [ProductsController::class, 'increaseStock']);
        Route::patch('/{id}/decrease-stock', [ProductsController::class, 'decreaseStock']);

        Route::middleware('admin')->group(function () {
            Route::post('/', [ProductsController::class, 'store']);
            Route::delete('/{id}', [ProductsController::class, 'destroy']);
        });
    });
});