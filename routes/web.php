<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ContactController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');

Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');

Route::view('/admin', 'admin');
Route::get('/admin/{any}', fn () => view('admin'))->where('any', '.*');
