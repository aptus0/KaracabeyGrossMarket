<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
