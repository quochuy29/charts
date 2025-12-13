<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;


Route::middleware(['auth:sanctum'])->group(function () {
    // API lấy thông tin user đang login
    Route::get('/get-user-login', [AuthController::class, 'getUserLogin']);
    Route::get('/equipments/tree', [EquipmentController::class, 'getTree']);
});
