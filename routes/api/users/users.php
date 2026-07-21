<?php

use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [UsersController::class, 'find']);
    Route::post('/', [UsersController::class, 'createUpdate']);
    Route::put('/{id}', [UsersController::class, 'createUpdate']);
    Route::put('/{id}/toggle-active', [UsersController::class, 'toggleActive']);
    Route::get('/{id}', [UsersController::class, 'findOne']);
    Route::delete('/{id}', [UsersController::class, 'delete']);
});
