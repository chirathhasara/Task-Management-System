<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome-auth');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');

Route::get('/tasks', [TaskController::class, 'indexView'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'createView'])->name('tasks.create');
Route::get('/tasks/recycle-bin', [TaskController::class, 'recycleBinView'])->name('tasks.recycle-bin');
Route::get('/tasks/{id}', [TaskController::class, 'showView'])->name('tasks.show');
Route::get('/tasks/{id}/edit', [TaskController::class, 'editView'])->name('tasks.edit');
