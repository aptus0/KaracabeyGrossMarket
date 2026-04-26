<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\FavoriteController;
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
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('throttle:api');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->middleware('throttle:api');

    Route::get('/cart', [CartController::class, 'show'])->middleware('throttle:api');
    Route::post('/cart/items', [CartController::class, 'store'])->middleware('throttle:api');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->middleware('throttle:api');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->middleware('throttle:api');
    Route::delete('/cart', [CartController::class, 'clear'])->middleware('throttle:api');

    Route::post('/checkout/paytr', [CheckoutController::class, 'store'])->middleware('throttle:payments');

    Route::middleware(['auth:api', 'throttle:api'])->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::apiResource('addresses', AddressController::class)->except(['show']);
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{product:slug}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{product:slug}', [FavoriteController::class, 'destroy']);
        Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
        Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
    });

    Route::middleware(['auth:api', 'throttle:payments'])->group(function (): void {
        Route::get('/payments/{payment}/status', [PaymentStatusController::class, 'show']);
        Route::post('/payments/{payment}/refunds', [RefundController::class, 'store']);
    });
});

Route::post('/paytr/callback', CallbackController::class)->middleware('throttle:paytr-callback');
