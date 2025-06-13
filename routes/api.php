<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Admin\ShowreelController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Admin\VueFinderController;
use App\Http\Controllers\Api\Admin\PageSeoController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\Admin\HomeGridController;
use App\Http\Controllers\Api\Admin\TblServiceController;

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy']);
Route::any('/vuefinder', VueFinderController::class);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/me', fn (Request $request) => $request->user());
    Route::get('/showreel', [ShowreelController::class, 'show']);
    Route::post('/showreel', [ShowreelController::class, 'update']);
    Route::post('/upload', [UploadController::class, 'store']);

    Route::get('/pages/home-seo', [PageSeoController::class, 'getHomeSeo']);
    Route::post('/pages/home-seo', [PageSeoController::class, 'updateHomeSeo']);
    Route::get('/pages/{pageName}-seo', [PageSeoController::class, 'getSeo']);
    Route::post('/pages/{pageName}-seo', [PageSeoController::class, 'updateSeo']);

    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'update']);
    Route::post('/settings/map', [SettingsController::class, 'updateMap']);

    Route::get('/home-grid', [HomeGridController::class, 'index']);
    Route::post('/home-grid', [HomeGridController::class, 'update']);


    Route::get('tbl-services', [TblServiceController::class, 'index']);
    Route::post('tbl-services', [TblServiceController::class, 'store']);
    Route::get('tbl-services/{id}', [TblServiceController::class, 'show']);
    Route::put('tbl-services/{id}', [TblServiceController::class, 'update']);
    Route::post('tbl-services/reorder', [TblServiceController::class, 'reorder']);
    Route::post('tbl-services/reorder', [TblServiceController::class, 'reorder']);
    Route::delete('tbl-services/{id}', [TblServiceController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


