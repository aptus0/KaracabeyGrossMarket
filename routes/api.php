<?php

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PaymentStatusController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Paytr\CallbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:api');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:api');

    Route::get('/products', [ProductController::class, 'index'])->middleware('throttle:api');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->middleware('throttle:api');

    Route::post('/checkout/paytr', [CheckoutController::class, 'store'])->middleware('throttle:payments');

    Route::middleware(['auth', 'throttle:payments'])->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
        Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
        Route::get('/payments/{payment}/status', [PaymentStatusController::class, 'show']);
        Route::post('/payments/{payment}/refunds', [RefundController::class, 'store']);
    });
});

Route::post('/paytr/callback', CallbackController::class)->middleware('throttle:paytr-callback');
