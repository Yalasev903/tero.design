<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Admin\ShowreelController;

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/admin/showreel', [ShowreelController::class, 'update']);
    Route::get('/me', fn (Request $request) => $request->user());
    Route::get('/showreel', [ShowreelController::class, 'show']);
    Route::post('/showreel', [ShowreelController::class, 'update']);
    Route::post('/upload', [UploadController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
