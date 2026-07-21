<?php

use App\Http\Controllers\Api\ItemsController;
use Illuminate\Support\Facades\Route;

Route::prefix('items')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [ItemsController::class, 'find']);
    Route::post('/', [ItemsController::class, 'createUpdate']);
    Route::put('/{id}', [ItemsController::class, 'createUpdate']);
    Route::put('/{id}/toggle-active', [ItemsController::class, 'toggleActive']);
    Route::get('/{id}', [ItemsController::class, 'findOne']);
    Route::delete('/{id}', [ItemsController::class, 'delete']);
});
