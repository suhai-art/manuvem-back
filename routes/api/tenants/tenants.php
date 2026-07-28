<?php

use App\Http\Controllers\Api\TenantsController;
use Illuminate\Support\Facades\Route;

Route::prefix('tenants')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [TenantsController::class, 'find'])->middleware('can:tenants.view');
    Route::post('/', [TenantsController::class, 'createUpdate'])->middleware('can:tenants.create');
    Route::put('/{id}', [TenantsController::class, 'createUpdate'])->middleware('can:tenants.update');
    Route::get('/{id}', [TenantsController::class, 'findOne'])->middleware('can:tenants.view');
    Route::delete('/{id}', [TenantsController::class, 'delete'])->middleware('can:tenants.delete');
});
