<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Admin\ShowreelController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Admin\VueFinderController;
use App\Http\Controllers\Api\Admin\PageSeoController;

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

    Route::get('/settings', function () {
    return DB::table('settings')->first();
    });

    Route::post('/settings', function (Request $request) {
        $data = $request->all();
        DB::table('settings')->updateOrInsert(['id' => 1], $data);
        return response()->json(['success' => true]);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


