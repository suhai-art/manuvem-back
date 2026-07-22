<?php

use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [UsersController::class, 'find'])->middleware('can:user.view');
    Route::post('/', [UsersController::class, 'createUpdate'])->middleware('can:user.create');
    Route::put('/{id}', [UsersController::class, 'createUpdate'])->middleware('can:user.update');
    Route::put('/{id}/toggle-active', [UsersController::class, 'toggleActive'])->middleware('can:user.update');
    Route::get('/{id}', [UsersController::class, 'findOne'])->middleware('can:user.view');
    Route::delete('/{id}', [UsersController::class, 'delete'])->middleware('can:user.delete');
});
