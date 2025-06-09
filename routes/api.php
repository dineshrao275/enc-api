<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'login_get'])->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route::middleware(['auth:sanctum','encdec'])->group( function () {
//     Route::apiResource('article', ArticleController::class);
// });

Route::middleware('auth:sanctum')->group( function () {
    Route::apiResource('article', ArticleController::class);
});
