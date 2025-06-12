<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\PortfolioApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\WarningController;
use App\Http\Controllers\Api\ScheduleApiController;
use Illuminate\Support\Facades\Auth;

// login dan register
Route::get('/register', [AuthApiController::class, 'showregister']);
Route::get('/login', [AuthApiController::class, 'showLogin']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

// buat post
Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{id}', [PostApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostApiController::class, 'store']);
 
    Route::post('/portfolios', [PortfolioApiController::class, 'store']);

    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
});

// buat portfolio
Route::middleware('web')->apiResource('portfolios', PortfolioApiController::class);

Route::get('/portfolios', [PortfolioApiController::class, 'index']);
Route::get('/portfolios/{id}', [PortfolioApiController::class, 'show']);

// buat schedule
// routes/web.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('schedules', ScheduleApiController::class);
});

// buat warning
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('warnings', WarningController::class);
});

// ambil data user dari api
Route::get('/user', function (Request $request) {
        return $request->user();
});
