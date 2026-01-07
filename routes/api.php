<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/books/search', [BookController::class, 'search']);
Route::apiResource('books', BookController::class);