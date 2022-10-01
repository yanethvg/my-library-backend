<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// my controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;

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
    Route::post('/{id}/{student_id}/return', [BookController::class, 'return'])->middleware('permission:books.return');
});

// students
Route::group(['prefix' => 'students' , 'middleware' => ['auth:sanctum']],function () {
    Route::get('/', [StudentController::class, 'index'])->middleware('permission:students.index');
    Route::get('/books', [StudentController::class, 'books'])->middleware('permission:students.books');
});

Route::group(['prefix' => 'roles' , 'middleware' => ['auth:sanctum']],function () {
    Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.index');
});

