<?php

use App\Http\Controllers\Api\RolesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', function (Request $request) {
        $user = $request->user();

        return [
            'authenticated' => auth()->check(),
            'id' => $user?->id,
            'email' => $user?->email,
            'guard' => auth()->getDefaultDriver(),
            'roles' => $user?->getRoleNames(),
            'permissions' => $user?->getAllPermissions()->pluck('name'),
            'can_role_view' => $user?->can('role.view'),
            'has_role_view' => $user?->hasPermissionTo('role.view'),
        ];
    });
    /* Route::get('/', [RolesController::class, 'find'])->middleware('permission:role.view'); */
    Route::post('/', [RolesController::class, 'createUpdate'])->middleware('can:role.create');
    Route::put('/{id}', [RolesController::class, 'createUpdate'])->middleware('can:role.update');
    Route::get('/{id}', [RolesController::class, 'findOne'])->middleware('can:role.view');
    Route::delete('/{id}', [RolesController::class, 'delete'])->middleware('can:role.delete');
});
