<?php

use App\Http\Controllers\Api\ClientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('clients')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ClientsController::class, 'find'])->middleware('can:client.view');
    Route::post('/', [ClientsController::class, 'createUpdate'])->middleware('can:client.create');
    Route::put('/{id}', [ClientsController::class, 'createUpdate'])->middleware('can:client.update');
    Route::put('/{id}/toggle-active', [ClientsController::class, 'toggleActive'])->middleware('can:client.update');
    Route::get('/{id}', [ClientsController::class, 'findOne'])->middleware('can:client.view');
    Route::delete('/{id}', [ClientsController::class, 'delete'])->middleware('can:client.delete');
});
