<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Thêm dấu ? sau 'any' và where('any', '.*') để bắt tất cả các route con và trang chủ
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');