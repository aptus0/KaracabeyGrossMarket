<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AuthLogController as AdminAuthLogController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ErpCariController as AdminErpCariController;
use App\Http\Controllers\Admin\ErpFaturaController as AdminErpFaturaController;
use App\Http\Controllers\Admin\ErpImportController as AdminErpImportController;
use App\Http\Controllers\Admin\ErpSayimController as AdminErpSayimController;
use App\Http\Controllers\Admin\FakeAuth2Controller as AdminFakeAuth2Controller;
use App\Http\Controllers\Admin\HomepageBlockController as AdminHomepageBlockController;
use App\Http\Controllers\Admin\MarketingSettingController as AdminMarketingSettingController;
use App\Http\Controllers\Admin\NavigationItemController as AdminNavigationItemController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Paytr\CheckoutPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $storefrontUrl = rtrim((string) config('commerce.domains.storefront', '/'), '/');

    if (app()->environment(['local', 'testing'])) {
        $host = $request->getHost();

        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return redirect()->away($storefrontUrl ?: '/');
        }
    }

    return redirect()->away($storefrontUrl ?: '/');
})->name('storefront.redirect');

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::get('/p/{order:checkout_ref}', [CheckoutPageController::class, 'show'])
    ->name('checkout.session');

Route::get('/oauth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.redirect');

Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.callback');

$adminPrefix = trim((string) config('admin_security.admin_prefix', 'admin'), '/');

Route::prefix($adminPrefix)->name('admin.')->middleware('admin.security')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
    });

    Route::get('/oauth2/authorize', [AdminFakeAuth2Controller::class, 'show'])->name('fake-auth2.show');
    Route::post('/oauth2/token', [AdminFakeAuth2Controller::class, 'store'])->name('fake-auth2.store');
    Route::get('/auth2', [AdminFakeAuth2Controller::class, 'trap'])->name('decoy.auth2');
    Route::get('/sso/login', [AdminFakeAuth2Controller::class, 'trap'])->name('decoy.sso');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('auth-logs', AdminAuthLogController::class)->name('auth-logs.index');
        Route::resource('products', AdminProductController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::post('products/bulk', [AdminProductController::class, 'bulkAction'])->name('products.bulk');
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
        Route::resource('payments', AdminPaymentController::class)->only(['index']);
        Route::resource('users', AdminUserController::class)->only(['index']);
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        Route::resource('pages', AdminPageController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('homepage-blocks', AdminHomepageBlockController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['homepage-blocks' => 'homepageBlock']);
        Route::resource('navigation', AdminNavigationItemController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['navigation' => 'navigationItem']);
        Route::resource('campaigns', AdminCampaignController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::resource('stories', AdminStoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('coupons', [AdminCampaignController::class, 'storeCoupon'])->name('coupons.store');
        Route::get('marketing', [AdminMarketingSettingController::class, 'edit'])->name('marketing.edit');
        Route::put('marketing', [AdminMarketingSettingController::class, 'update'])->name('marketing.update');

        // ── ERP Modülleri ────────────────────────────────────────────
        Route::get('erp/import', [AdminErpImportController::class, 'index'])->name('erp.import');
        Route::post('erp/test-connection', [AdminErpImportController::class, 'testConnection'])->name('erp.test-connection');
        Route::post('erp/import', [AdminErpImportController::class, 'import'])->name('erp.import.run');
        Route::get('erp/import/status', [AdminErpImportController::class, 'status'])->name('erp.import.status');
        Route::get('erp/fatura', [AdminErpFaturaController::class, 'index'])->name('erp.fatura');
        Route::get('erp/fatura/{id}', [AdminErpFaturaController::class, 'show'])->name('erp.fatura.show');
        Route::post('erp/fatura/{id}/sync', [AdminErpFaturaController::class, 'sync'])->name('erp.fatura.sync');
        Route::get('erp/cari', [AdminErpCariController::class, 'index'])->name('erp.cari');
        Route::get('erp/cari/{id}', [AdminErpCariController::class, 'show'])->name('erp.cari.show');
        Route::get('erp/sayim', [AdminErpSayimController::class, 'index'])->name('erp.sayim');
        Route::get('erp/sayim/{id}', [AdminErpSayimController::class, 'show'])->name('erp.sayim.show');
    });

    Route::get('/{adminDecoyPath}', [AdminFakeAuth2Controller::class, 'trap'])
        ->where('adminDecoyPath', '.*')
        ->name('decoy.catch');
});

Route::middleware('admin.security')->group(function (): void {
    Route::get('/administrator', [AdminFakeAuth2Controller::class, 'trap'])->name('admin.decoy.administrator');
    Route::get('/admin.php', [AdminFakeAuth2Controller::class, 'trap'])->name('admin.decoy.php');
    Route::get('/wp-admin', [AdminFakeAuth2Controller::class, 'trap'])->name('admin.decoy.wp-admin');
    Route::get('/cpanel', [AdminFakeAuth2Controller::class, 'trap'])->name('admin.decoy.cpanel');
});
