<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::middleware(['auth:sanctum'])->group(function () {
    // API lấy thông tin user đang login
    Route::get('/get-user-login', [AuthController::class, 'getUserLogin']);
});
