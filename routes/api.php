<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('test')->group(function() {
    Route::get('/', function() {
        return response()->json([
            'status' => 'success',
            'mensagem' => 'Working'
        ]);
    });
});
