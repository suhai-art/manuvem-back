<?php

use App\Http\Controllers\Api\RolesController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [RolesController::class, 'find'])->middleware('permission:role.view');
    Route::post('/', [RolesController::class, 'createUpdate'])->middleware('can:role.create');
    Route::put('/{id}', [RolesController::class, 'createUpdate'])->middleware('can:role.update');
    Route::get('/{id}', [RolesController::class, 'findOne'])->middleware('can:role.view');
    Route::delete('/{id}', [RolesController::class, 'delete'])->middleware('can:role.delete');
});
