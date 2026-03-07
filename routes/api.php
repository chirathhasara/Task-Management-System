<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-current', [AuthController::class, 'logoutCurrentDevice']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });

    // Legacy route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
