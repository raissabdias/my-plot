<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShelfController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/books/search', [BookController::class, 'search']);
    Route::apiResource('books', BookController::class);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/reading-goal', [DashboardController::class, 'storeGoal']);

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);

    Route::apiResource('shelf', ShelfController::class);
});

