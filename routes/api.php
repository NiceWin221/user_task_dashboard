<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\CheckUserStatus;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->middleware(CheckUserStatus::class);

// Task Routes
Route::post('/tasks', [TaskController::class, 'create'])->middleware('auth:sanctum');
Route::get('/tasks', [TaskController::class, 'index'])->middleware('auth:sanctum');
Route::put('/tasks/{id}', [TaskController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/tasks/{id}', [TaskController::class, 'show']);

// User Routes
Route::post('/users', [AuthController::class, 'register'])->middleware(['auth:sanctum', CheckUserRole::class . ':admin']);
Route::get('/users', [UserController::class, 'index'])->middleware(['auth:sanctum', CheckUserRole::class . ':admin,manager']);
Route::get('/user', [UserController::class, 'getUserByToken'])->middleware('auth:sanctum');
Route::get('/user/{id}', [UserController::class, 'getUserById'])->middleware('auth:sanctum');

// ActivityLog Routes
Route::get('/logs', [ActivityLogController::class, 'index'])->middleware(['auth:sanctum', CheckUserRole::class . ':admin']);
