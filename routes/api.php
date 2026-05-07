<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GIS\SpatialDataController;

Route::middleware(['auth:sanctum'])->prefix('spatial-data')->group(function () {
    // Static routes dulu — urutan ini penting
    Route::get('/',           [SpatialDataController::class, 'apiIndex']);
    Route::get('/list',       [SpatialDataController::class, 'apiList']);
    Route::get('/categories', [SpatialDataController::class, 'categories']);
    Route::get('/export',     [SpatialDataController::class, 'export']);
    Route::post('/import',    [SpatialDataController::class, 'import']);
    Route::post('/',          [SpatialDataController::class, 'apiStore']);

    // Dynamic routes terakhir — tambahkan whereNumber agar tidak bentrok
    Route::get('/{id}',    [SpatialDataController::class, 'apiShow'])->whereNumber('id');
    Route::put('/{id}',    [SpatialDataController::class, 'apiUpdate'])->whereNumber('id');
    Route::delete('/{id}', [SpatialDataController::class, 'apiDestroy'])->whereNumber('id');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
