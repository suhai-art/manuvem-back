<?php

use App\Http\Controllers\Api\ClientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('clients')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ClientsController::class, 'find']);
    Route::post('/', [ClientsController::class, 'createUpdate']);
    Route::put('/{id}', [ClientsController::class, 'createUpdate']);
    Route::put('/{id}/toggle-active', [ClientsController::class, 'toggleActive']);
    Route::get('/{id}', [ClientsController::class, 'findOne']);
    Route::delete('/{id}', [ClientsController::class, 'delete']);
});
