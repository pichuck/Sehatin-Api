<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\ResepController;
use App\Http\Controllers\Api\UserController;


Route::get('/halo', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/users', [App\Http\Controllers\Api\AuthController::class, 'index']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUser']);
Route::apiResource('users', UserController::class);





Route::apiResource('resep', ResepController::class);
Route::apiResource('artikel', ArtikelController::class);