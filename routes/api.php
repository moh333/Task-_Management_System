<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Sanctum Authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard Route
    Route::get('/dashboard', DashboardController::class);

    // Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notification/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Projects Module Routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/project/create', [ProjectController::class, 'store']);
    Route::get('/project/show/{id}', [ProjectController::class, 'show']);
    Route::match(['put', 'patch'], '/project/update/{id}', [ProjectController::class, 'update']);
    Route::delete('/project/delete/{id}', [ProjectController::class, 'destroy']);

    // Tasks Module Routes
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/task/create', [TaskController::class, 'store']);
    Route::get('/task/show/{id}', [TaskController::class, 'show']);
    Route::match(['put', 'patch'], '/task/update/{id}', [TaskController::class, 'update']);
    Route::delete('/task/delete/{id}', [TaskController::class, 'destroy']);
});
