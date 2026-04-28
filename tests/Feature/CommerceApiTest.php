<?php

use App\Models\CartCoupon;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function commerceTenant(): Tenant
{
    return Tenant::query()->create([
        'name' => 'Karacabey Gross Market',
        'slug' => 'karacabey-gross-market',
        'domain' => 'karacabeygrossmarket.com',
    ]);
}

it('lists categories and filters products by category', function (): void {
    $tenant = commerceTenant();
    $category = Category::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Meyve Sebze',
        'slug' => 'meyve-sebze',
    ]);

    $matching = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Karacabey Domates 1 Kg',
        'slug' => 'karacabey-domates-1-kg',
        'price_cents' => 3890,
        'stock_quantity' => 10,
    ]);

    Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Taze Ekmek',
        'slug' => 'taze-ekmek',
        'price_cents' => 1250,
        'stock_quantity' => 20,
    ]);

    $category->products()->attach($matching);

    $this->getJson('/api/v1/categories')
        ->assertOk()
        ->assertJsonPath('data.0.slug', 'meyve-sebze');

    $this->getJson('/api/v1/products?category=meyve-sebze')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'karacabey-domates-1-kg');
});

it('manages a guest cart with a portable cart token', function (): void {
    $tenant = commerceTenant();
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Gunluk Sut 1 L',
        'slug' => 'gunluk-sut-1-l',
        'price_cents' => 4490,
        'stock_quantity' => 10,
    ]);
    Coupon::query()->create([
        'tenant_id' => $tenant->id,
        'code' => 'KGM10',
        'discount_type' => 'fixed',
        'discount_value' => 1000,
        'minimum_order_cents' => 10000,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/v1/cart/items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertCreated();

    $cartToken = $response->json('data.cart_token');
    $cartItemId = $response->json('data.items.0.id');

    expect($cartToken)->toBeString()->not->toBeEmpty();

    $this->withHeader('X-Cart-Token', $cartToken)
        ->patchJson('/api/v1/cart/items/'.$cartItemId, ['quantity' => 3])
        ->assertOk()
        ->assertJsonPath('data.total_cents', 13470);

    $this->withHeader('X-Cart-Token', $cartToken)
        ->postJson('/api/v1/cart/coupon', ['code' => 'kgm10'])
        ->assertOk()
        ->assertJsonPath('data.code', 'KGM10')
        ->assertJsonPath('data.discount_cents', 1000)
        ->assertJsonPath('data.total_cents', 12470);

    $this->withHeader('X-Cart-Token', $cartToken)
        ->getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonPath('data.items.0.quantity', 3)
        ->assertJsonPath('data.applied_coupon.code', 'KGM10')
        ->assertJsonPath('data.total_cents', 12470);

    $this->withHeader('X-Cart-Token', $cartToken)
        ->patchJson('/api/v1/cart/items/'.$cartItemId, ['quantity' => 1])
        ->assertOk()
        ->assertJsonPath('data.applied_coupon', null)
        ->assertJsonPath('data.total_cents', 4490);
});

it('manages authenticated addresses and favorites', function (): void {
    $tenant = commerceTenant();
    $user = User::factory()->create([
        'email' => 'customer@example.com',
        'phone' => '5551112233',
    ]);
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Taze Ekmek',
        'slug' => 'taze-ekmek',
        'price_cents' => 1250,
        'stock_quantity' => 20,
    ]);

    $token = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'password',
        'device_name' => 'feature-test',
    ])
        ->assertOk()
        ->json('token');

    $this->withToken($token)
        ->postJson('/api/v1/addresses', [
            'title' => 'Ev',
            'recipient_name' => 'Test Musteri',
            'phone' => '5551112233',
            'city' => 'Bursa',
            'district' => 'Karacabey',
            'address_line' => 'Merkez Mahallesi',
            'is_default' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('data.is_default', true);

    $this->withToken($token)
        ->postJson('/api/v1/favorites/'.$product->slug)
        ->assertCreated()
        ->assertJsonPath('data.slug', 'taze-ekmek');

    $this->withToken($token)
        ->getJson('/api/v1/favorites')
        ->assertOk()
        ->assertJsonPath('data.0.slug', 'taze-ekmek');

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertOk();

    expect($user->apiTokens()->count())->toBe(0);
});

it('rejects invalid bearer tokens', function (): void {
    $this->withToken('not-a-real-token')
        ->getJson('/api/v1/auth/me')
        ->assertUnauthorized();
});

it('returns safe product suggestions for header search', function (): void {
    $tenant = commerceTenant();

    Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Gunluk Sut 1 L',
        'slug' => 'gunluk-sut-1-l',
        'brand' => 'KGM',
        'price_cents' => 4490,
        'stock_quantity' => 10,
        'image_url' => 'javascript:alert(1)',
        'is_active' => true,
    ]);

    $this->getJson('/api/v1/products/suggest?q=sut')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Gunluk Sut 1 L')
        ->assertJsonPath('data.0.price', '₺44,90')
        ->assertJsonPath('data.0.image_url', null);

    $this->getJson('/api/v1/products/suggest?q=a')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('creates a provider-neutral checkout from a guest cart and clears it after callback', function (): void {
    config([
        'paytr.merchant_id' => 'merchant-id',
        'paytr.merchant_key' => 'merchant-key',
        'paytr.merchant_salt' => 'merchant-salt',
    ]);

    Http::fake([
        config('paytr.endpoints.iframe_token') => Http::response([
            'status' => 'success',
            'token' => 'iframe-token-123',
        ]),
    ]);

    $tenant = commerceTenant();
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Gunluk Sut 1 L',
        'slug' => 'gunluk-sut-1-l',
        'price_cents' => 4490,
        'stock_quantity' => 10,
    ]);

    $cartToken = 'guest-cart-token';
    CartItem::query()->create([
        'tenant_id' => $tenant->id,
        'cart_token' => $cartToken,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    Coupon::query()->create([
        'tenant_id' => $tenant->id,
        'code' => 'KGM10',
        'discount_type' => 'fixed',
        'discount_value' => 1000,
        'minimum_order_cents' => 8000,
        'is_active' => true,
    ]);

    $this->withHeader('X-Cart-Token', $cartToken)
        ->postJson('/api/v1/cart/coupon', ['code' => 'KGM10'])
        ->assertOk()
        ->assertJsonPath('data.total_cents', 7980);

    $response = $this->postJson('/api/v1/c', [
        'cart_token' => $cartToken,
        'customer' => [
            'name' => 'Test Musteri',
            'email' => 'test@example.com',
            'phone' => '5551112233',
        ],
        'shipping' => [
            'city' => 'Bursa',
            'district' => 'Karacabey',
            'address' => 'Karacabey merkez',
        ],
    ])->assertCreated();

    expect($response->json('data.iframe_token'))->toBe('iframe-token-123')
        ->and($response->json('data.total_cents'))->toBe(7980)
        ->and($product->fresh()->stock_quantity)->toBe(8);

    expect($response->json('data.checkout_url'))->toContain('/p/')
        ->not->toContain($response->json('data.merchant_oid'))
        ->not->toContain('paytr');

    $payment = Payment::query()->firstOrFail();
    $payload = [
        'merchant_oid' => $payment->merchant_oid,
        'status' => 'success',
        'total_amount' => '7980',
        'payment_amount' => '7980',
        'payment_type' => 'card',
    ];
    $payload['hash'] = base64_encode(hash_hmac(
        'sha256',
        $payload['merchant_oid'].'merchant-salt'.$payload['status'].$payload['total_amount'],
        'merchant-key',
        true
    ));

    $this->post('/api/cb/p', $payload)
        ->assertOk()
        ->assertSeeText('OK');

    expect(CartItem::query()->where('cart_token', $cartToken)->count())->toBe(0)
        ->and(CartCoupon::query()->where('cart_token', $cartToken)->count())->toBe(0)
        ->and(Order::query()->firstOrFail()->fresh()->discount_cents)->toBe(1000)
        ->and(Order::query()->firstOrFail()->fresh()->status->value)->toBe('paid')
        ->and($product->fresh()->stock_quantity)->toBe(8);
});

it('reuses the same pending checkout session for duplicate checkout submissions', function (): void {
    config([
        'paytr.merchant_id' => 'merchant-id',
        'paytr.merchant_key' => 'merchant-key',
        'paytr.merchant_salt' => 'merchant-salt',
    ]);

    Http::fake([
        config('paytr.endpoints.iframe_token') => Http::response([
            'status' => 'success',
            'token' => 'iframe-token-456',
        ]),
    ]);

    $tenant = commerceTenant();
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Gunluk Sut 1 L',
        'slug' => 'gunluk-sut-1-l',
        'price_cents' => 4490,
        'stock_quantity' => 12,
    ]);

    $cartToken = 'duplicate-checkout-cart';
    $checkoutKey = 'same-checkout-key';

    CartItem::query()->create([
        'tenant_id' => $tenant->id,
        'cart_token' => $cartToken,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $payload = [
        'cart_token' => $cartToken,
        'customer' => [
            'name' => 'Test Musteri',
            'email' => 'duplicate@example.com',
            'phone' => '5551112233',
        ],
        'shipping' => [
            'city' => 'Bursa',
            'district' => 'Karacabey',
            'address' => 'Karacabey merkez',
        ],
        'checkout_key' => $checkoutKey,
    ];

    $firstResponse = $this->withHeader('X-Checkout-Key', $checkoutKey)
        ->postJson('/api/v1/c', $payload)
        ->assertCreated();

    $secondResponse = $this->withHeader('X-Checkout-Key', $checkoutKey)
        ->postJson('/api/v1/c', $payload)
        ->assertOk();

    expect($firstResponse->json('data.merchant_oid'))->toBe($secondResponse->json('data.merchant_oid'))
        ->and($firstResponse->json('data.iframe_token'))->toBe('iframe-token-456')
        ->and(Order::query()->count())->toBe(1)
        ->and(Payment::query()->count())->toBe(1)
        ->and($product->fresh()->stock_quantity)->toBe(10);

    Http::assertSentCount(1);
});
