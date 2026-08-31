<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ServiceOrderApprovalController;
use App\Http\Controllers\UserController;
use App\Interfaces\Http\Controllers\Customer\CreateCustomerController;
use App\Interfaces\Http\Controllers\Customer\DeleteCustomerController;
use App\Interfaces\Http\Controllers\Customer\FindCustomerController;
use App\Interfaces\Http\Controllers\Customer\ListCustomersController;
use App\Interfaces\Http\Controllers\Customer\UpdateCustomerController;
use App\Interfaces\Http\Controllers\Product\CreateProductController;
use App\Interfaces\Http\Controllers\Product\DecreaseStockController;
use App\Interfaces\Http\Controllers\Product\DeleteProductController;
use App\Interfaces\Http\Controllers\Product\FindProductController;
use App\Interfaces\Http\Controllers\Product\IncreaseStockController;
use App\Interfaces\Http\Controllers\Product\ListProductsController;
use App\Interfaces\Http\Controllers\Product\UpdateProductController;
use App\Interfaces\Http\Controllers\Vehicule\CreateVehiculeController;
use App\Interfaces\Http\Controllers\Vehicule\DeleteVehiculeController;
use App\Interfaces\Http\Controllers\Vehicule\FindVehiculeByCustomerController;
use App\Interfaces\Http\Controllers\Vehicule\FindVehiculeController;
use App\Interfaces\Http\Controllers\Vehicule\ListVehiculesController;
use App\Interfaces\Http\Controllers\Vehicule\UpdateVehiculeController;
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
        Route::get('/', ListCustomersController::class);
        Route::get('/{id}', FindCustomerController::class);
        Route::put('/{id}', UpdateCustomerController::class);

        Route::middleware('admin')->group(function () {
            Route::post('/', CreateCustomerController::class);
            Route::delete('/{id}', DeleteCustomerController::class);
        });
    });

    // Listagem de veículos por cliente
    Route::middleware('manager')->get(
        '/customers/{customerId}/vehicules',
        FindVehiculeByCustomerController::class
    );

    // CRUD de veículos — apenas Admin e Gerente
    Route::middleware('manager')->prefix('vehicules')->group(function () {
        Route::get('/', ListVehiculesController::class);
        Route::get('/{id}', FindVehiculeController::class);
        Route::put('/{id}', UpdateVehiculeController::class);

        Route::middleware('admin')->group(function () {
            Route::post('/', CreateVehiculeController::class);
            Route::delete('/{id}', DeleteVehiculeController::class);
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
        Route::get('/', ListProductsController::class);
        Route::get('/{id}', FindProductController::class);
        Route::put('/{id}', UpdateProductController::class);

        // Atualização de estoque
        Route::patch('/{id}/increase-stock', IncreaseStockController::class);
        Route::patch('/{id}/decrease-stock', DecreaseStockController::class);

        Route::middleware('admin')->group(function () {
            Route::post('/', CreateProductController::class);
            Route::delete('/{id}', DeleteProductController::class);
        });
    });
});
