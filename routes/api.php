<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
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
    // Projects Module Routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/project/create', [ProjectController::class, 'store']);
    Route::get('/project/show/{id}', [ProjectController::class, 'show']);
    Route::match(['put', 'patch'], '/project/update/{id}', [ProjectController::class, 'update']);
    Route::delete('/project/delete/{id}', [ProjectController::class, 'destroy']);

    
});
