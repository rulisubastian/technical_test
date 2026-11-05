<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,ProductController,DashboardController,CustomerController,OrderController,ReportController,StockController};

Route::get('/health', fn () => response()->json([
    'success' => true,
    'message' => 'OK',
    'data' => [
        'database' => \Illuminate\Support\Facades\DB::select('select 1') ? 'ok':'down',
        'redis'=>\Illuminate\Support\Facades\Redis::ping() ? 'ok':'down'
    ]
]));
Route::prefix('auth')->group(function(){
    Route::middleware('throttle:login')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
    });
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/logout',[AuthController::class,'logout']);
        Route::get('/me',[AuthController::class,'me']);
    });
});

Route::middleware(['auth:sanctum','request.id'])->group(function(){
    Route::apiResource('products',ProductController::class); 
    Route::post('products/{product}/stock-adjustments',[StockController::class,'adjust']); 
    Route::get('products/{product}/stock-movements',[StockController::class,'index']);
    Route::apiResource('customers',CustomerController::class);
    Route::apiResource('orders',OrderController::class)->only(['index','store','show']); 
    Route::post('orders/{order}/pay',[OrderController::class,'pay']);
    Route::post('orders/{order}/ship',[OrderController::class,'ship']);
    Route::post('orders/{order}/complete',[OrderController::class,'complete']);
    Route::post('orders/{order}/cancel',[OrderController::class,'cancel']);
    Route::get('orders/{order}/invoice',[OrderController::class,'invoice']);
    Route::get('dashboard/summary',[ReportController::class,'summary']);
    Route::get('reports/sales',[ReportController::class,'sales']);
    Route::get('reports/top-products',[ReportController::class,'topProducts']);
});
