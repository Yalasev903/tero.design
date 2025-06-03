<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkflowController;

// Главная страница сайта (home.blade.php)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');

Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow');

Route::get('/contact', function () {
    return view('contact'); // или заглушка
})->name('contact');

Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
