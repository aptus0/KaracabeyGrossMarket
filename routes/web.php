<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomepageBlockController as AdminHomepageBlockController;
use App\Http\Controllers\Admin\MarketingSettingController as AdminMarketingSettingController;
use App\Http\Controllers\Admin\NavigationItemController as AdminNavigationItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Paytr\CheckoutPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => config('app.name'),
    'status' => 'ok',
]));

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::get('/p/{order:checkout_ref}', [CheckoutPageController::class, 'show'])
    ->name('checkout.session');

Route::get('/oauth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.redirect');

Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.callback');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('products', AdminProductController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store']);
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
        Route::resource('payments', AdminPaymentController::class)->only(['index']);
        Route::resource('users', AdminUserController::class)->only(['index']);
        Route::resource('pages', AdminPageController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('homepage-blocks', AdminHomepageBlockController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['homepage-blocks' => 'homepageBlock']);
        Route::resource('navigation', AdminNavigationItemController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['navigation' => 'navigationItem']);
        Route::resource('campaigns', AdminCampaignController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('coupons', [AdminCampaignController::class, 'storeCoupon'])->name('coupons.store');
        Route::get('marketing', [AdminMarketingSettingController::class, 'edit'])->name('marketing.edit');
        Route::put('marketing', [AdminMarketingSettingController::class, 'update'])->name('marketing.update');
    });
});
