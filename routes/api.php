<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// my controllers
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');