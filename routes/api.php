<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookController::class, 'index']);
Route::post('/books', [BookController::class, 'store']);
Route::get('/books/search', [BookController::class, 'search']);
Route::put('/books/{book}', [BookController::class, 'update']);