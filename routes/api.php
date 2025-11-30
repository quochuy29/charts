<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Endpoint lấy cây thư mục (Sidebar)
Route::get('/sidebar-tree', [ChartController::class, 'getSidebarTree']);

// Endpoint lấy dữ liệu biểu đồ (Chart & Table)
Route::get('/chart-data', [ChartController::class, 'getChartData']);