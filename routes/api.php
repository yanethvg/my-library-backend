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
    Route::get('/', [GenreController::class, 'index'])->middleware('permission:genres.index');
});

// books
Route::group(['prefix' => 'books','middleware' => ['auth:sanctum']],function () {
    Route::get('/', [BookController::class, 'index'])->middleware('permission:books.index');
    Route::get('/{id}', [BookController::class, 'show'])->middleware('permission:books.show');
    Route::post('/', [BookController::class, 'store'])->middleware('permission:books.store');
    Route::post('/{id}/borrow', [BookController::class, 'borrow'])->middleware('permission:books.checkout');
    Route::post('/{id}/return', [BookController::class, 'return'])->middleware('permission:books.return');
});