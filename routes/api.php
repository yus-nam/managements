<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\SalesController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ProductController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/sales/purchase', [SaleController::class, 'purchase']);

// セールス関連のルート
Route::prefix('sales')->group(function () {
    Route::get('/purchase', [SaleController::class, 'purchase']);
    // 他のセールス関連ルートもここに追加できます
});

 Route::get('/products', [ProductController::class, 'index']);
