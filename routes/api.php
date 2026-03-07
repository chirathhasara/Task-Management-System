<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/logout-current', [AuthController::class, 'logoutCurrentDevice']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/trashed', [TaskController::class, 'trashed']);
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
        Route::patch('/{id}/complete', [TaskController::class, 'markAsCompleted']);
        Route::patch('/{id}/pending', [TaskController::class, 'markAsPending']);
        Route::post('/{id}/restore', [TaskController::class, 'restore']);
        Route::delete('/{id}/force', [TaskController::class, 'forceDestroy']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
