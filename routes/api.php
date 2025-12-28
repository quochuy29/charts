<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']); // Thêm logout

// Protected routes (Cần Token)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-user-login', [AuthController::class, 'getUserLogin']);
    Route::get('/equipments/tree', [EquipmentController::class, 'getTree']);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
});