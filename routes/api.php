<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// my controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\BookController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// genres
Route::group(['prefix' => 'genres','middleware' => ['auth:sanctum']],function () {
    Route::get('/', [GenreController::class, 'index']);
});

// books
Route::group(['prefix' => 'books','middleware' => ['auth:sanctum']],function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::post('/', [BookController::class, 'store']);
    Route::post('/{id}/borrow', [BookController::class, 'borrow']);
    Route::post('/{id}/return', [BookController::class, 'return']);
});