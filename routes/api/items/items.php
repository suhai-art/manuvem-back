<?php

use App\Http\Controllers\Api\ItemsController;
use Illuminate\Support\Facades\Route;

Route::prefix('items')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ItemsController::class, 'find'])->middleware('can:item.view');
    Route::post('/', [ItemsController::class, 'createUpdate'])->middleware('can:item.create');
    Route::put('/{id}', [ItemsController::class, 'createUpdate'])->middleware('can:item.update');
    Route::put('/{id}/toggle-active', [ItemsController::class, 'toggleActive'])->middleware('can:item.update');
    Route::get('/{id}', [ItemsController::class, 'findOne'])->middleware('can:item.view');
    Route::delete('/{id}', [ItemsController::class, 'delete'])->middleware('can:item.delete');
});
