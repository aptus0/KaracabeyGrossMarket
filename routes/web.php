<?php

use App\Http\Controllers\Paytr\CheckoutPageController;
use Illuminate\Support\Facades\Route;

Route::get('/checkout/paytr/{order:merchant_oid}', [CheckoutPageController::class, 'show'])
    ->name('paytr.checkout');
