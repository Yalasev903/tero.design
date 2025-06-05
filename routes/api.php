<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/me', fn (Request $request) => $request->user());
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
