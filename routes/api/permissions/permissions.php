<?php

use App\Http\Controllers\Api\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')->middleware(['auth:sanctum'])->group(function () {
    // Read-only listing of all permissions.
    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('can:permissions.view');

    // Creation endpoint removed: POST is not allowed.
    Route::post('/', [PermissionController::class, 'store'])
        ->middleware('can:permissions.manage');

    // Role management lives under permissions.* (requires permissions.manage).
    Route::post('/roles', [PermissionController::class, 'createRole'])
        ->middleware('can:permissions.manage');

    Route::post('/roles/{role}/attach', [PermissionController::class, 'attachPermission'])
        ->middleware('can:permissions.manage');

    Route::post('/roles/{role}/sync', [PermissionController::class, 'syncPermissions'])
        ->middleware('can:permissions.manage');
});
