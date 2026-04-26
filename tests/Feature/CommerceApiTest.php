<?php

use App\Models\CartItem;
use App\Models\Category;
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
        ->getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonPath('data.items.0.quantity', 3);
});

it('manages authenticated addresses and favorites', function (): void {
    $tenant = commerceTenant();
    $user = User::factory()->create(['email' => 'customer@example.com']);
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Taze Ekmek',
        'slug' => 'taze-ekmek',
        'price_cents' => 1250,
        'stock_quantity' => 20,
    ]);

    $token = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'feature-test',
    ])
        ->assertOk()
        ->json('data.token');

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

it('creates a paytr checkout from a guest cart and clears it after callback', function (): void {
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

    $response = $this->postJson('/api/v1/checkout/paytr', [
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
        ->and($product->fresh()->stock_quantity)->toBe(8);

    $payment = Payment::query()->firstOrFail();
    $payload = [
        'merchant_oid' => $payment->merchant_oid,
        'status' => 'success',
        'total_amount' => '8980',
        'payment_amount' => '8980',
        'payment_type' => 'card',
    ];
    $payload['hash'] = base64_encode(hash_hmac(
        'sha256',
        $payload['merchant_oid'].'merchant-salt'.$payload['status'].$payload['total_amount'],
        'merchant-key',
        true
    ));

    $this->post('/api/paytr/callback', $payload)
        ->assertOk()
        ->assertSeeText('OK');

    expect(CartItem::query()->where('cart_token', $cartToken)->count())->toBe(0)
        ->and(Order::query()->firstOrFail()->fresh()->status->value)->toBe('paid')
        ->and($product->fresh()->stock_quantity)->toBe(8);
});
