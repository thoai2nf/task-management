<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;

// Broadcasting authentication endpoint
Route::post('/broadcasting/auth', function (Request $request) {
    return auth('api')->user();
});

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Projects
    Route::apiResource('projects', ProjectController::class);

    // Tasks
    Route::prefix('projects/{projectId}')->group(function () {
        Route::get('tasks', [TaskController::class, 'index']);
        Route::post('tasks', [TaskController::class, 'store']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('{id}', [TaskController::class, 'show']);
        Route::put('{id}', [TaskController::class, 'update']);
        Route::delete('{id}', [TaskController::class, 'destroy']);
        Route::post('update-order', [TaskController::class, 'updateOrder']);
    });
});
