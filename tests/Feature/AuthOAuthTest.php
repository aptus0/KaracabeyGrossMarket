<?php

use App\Data\Auth\SocialUserData;
use App\Models\CartCoupon;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Auth\OAuthProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function authTenant(): Tenant
{
    return Tenant::query()->create([
        'name' => 'Karacabey Gross Market',
        'slug' => 'karacabey-gross-market',
        'domain' => 'karacabeygrossmarket.com',
    ]);
}

it('reports social providers as disabled when credentials are missing', function (): void {
    config([
        'services.google.oauth_client_id' => null,
        'services.google.oauth_client_secret' => null,
        'services.facebook.client_id' => null,
        'services.facebook.client_secret' => null,
    ]);

    $this->getJson('/api/v1/auth/providers')
        ->assertOk()
        ->assertJsonPath('data.google.enabled', false)
        ->assertJsonPath('data.facebook.enabled', false);
});

it('claims a guest cart on credential login', function (): void {
    $tenant = authTenant();
    $user = User::factory()->create([
        'email' => 'customer@example.com',
        'phone' => '5551112233',
    ]);
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Gunluk Sut 1 L',
        'slug' => 'gunluk-sut-1-l',
        'price_cents' => 4490,
        'stock_quantity' => 10,
    ]);

    CartItem::query()->create([
        'tenant_id' => $tenant->id,
        'cart_token' => 'guest-cart-token',
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $coupon = Coupon::query()->create([
        'tenant_id' => $tenant->id,
        'code' => 'LOGIN25',
        'discount_type' => 'fixed',
        'discount_value' => 2500,
        'minimum_order_cents' => 0,
        'is_active' => true,
    ]);
    CartCoupon::query()->create([
        'tenant_id' => $tenant->id,
        'cart_token' => 'guest-cart-token',
        'coupon_id' => $coupon->id,
    ]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => '5551112233',
        'password' => 'password',
        'device_name' => 'feature-test',
        'cart_token' => 'guest-cart-token',
    ])
        ->assertOk()
        ->assertJsonPath('user.email', 'customer@example.com');

    expect(CartItem::query()->where('cart_token', 'guest-cart-token')->count())->toBe(0)
        ->and(CartCoupon::query()->where('cart_token', 'guest-cart-token')->count())->toBe(0)
        ->and(CartCoupon::query()->where('user_id', $user->id)->first()?->coupon_id)->toBe($coupon->id)
        ->and(CartItem::query()->where('user_id', $user->id)->first()?->quantity)->toBe(2);
});

it('links a matching email during social callback and issues an api token', function (): void {
    config(['services.storefront.url' => 'http://127.0.0.1:3000']);

    $user = User::factory()->create([
        'email' => 'customer@example.com',
        'google_id' => null,
    ]);

    $this->mock(OAuthProviderManager::class, function ($mock): void {
        $mock->shouldReceive('isEnabled')->once()->with('google')->andReturn(true);
        $mock->shouldReceive('userFromCode')
            ->once()
            ->with('google', 'oauth-code')
            ->andReturn(new SocialUserData(
                provider: 'google',
                providerId: 'google-123',
                email: 'customer@example.com',
                name: 'Customer Example',
                avatarUrl: 'https://example.com/avatar.png',
            ));
    });

    $response = $this
        ->withSession(['oauth_state_google' => 'expected-state'])
        ->get('/oauth/google/callback?state=expected-state&code=oauth-code');

    $response->assertRedirect();

    $location = (string) $response->headers->get('Location');
    $hash = parse_url($location, PHP_URL_FRAGMENT);

    parse_str((string) $hash, $params);

    expect($location)->toContain('/auth/callback#')
        ->and($params['token']['token'] ?? null)->toBeString()
        ->and($params['provider'] ?? null)->toBe('google');

    $user->refresh();

    expect($user->google_id)->toBe('google-123')
        ->and($user->avatar_url)->toBe('https://example.com/avatar.png')
        ->and($user->apiTokens()->count())->toBe(1);

    $this->withToken($params['token']['token'])
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'customer@example.com');
});

it('creates a new user from a social callback when no email match exists', function (): void {
    config(['services.storefront.url' => 'http://127.0.0.1:3000']);

    $this->mock(OAuthProviderManager::class, function ($mock): void {
        $mock->shouldReceive('isEnabled')->once()->with('facebook')->andReturn(true);
        $mock->shouldReceive('userFromCode')
            ->once()
            ->with('facebook', 'oauth-code')
            ->andReturn(new SocialUserData(
                provider: 'facebook',
                providerId: 'facebook-456',
                email: 'fresh@example.com',
                name: 'Fresh Customer',
                avatarUrl: null,
            ));
    });

    $response = $this
        ->withSession(['oauth_state_facebook' => 'expected-state'])
        ->get('/oauth/facebook/callback?state=expected-state&code=oauth-code');

    $response->assertRedirect();

    $user = User::query()->where('email', 'fresh@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user?->facebook_id)->toBe('facebook-456')
        ->and($user?->email_verified_at)->not->toBeNull();
});
