<?php

use App\Models\AdminAuthLog;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows only admin users to view the admin dashboard', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create(['is_admin' => false]);

    $this->get('/admin')
        ->assertRedirect('/login');

    $this->actingAs($customer)
        ->get('/admin')
        ->assertForbidden();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSeeText('Dashboard');
});

it('lets admins update product price and stock', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $tenant = Tenant::query()->create([
        'name' => 'Karacabey Gross Market',
        'slug' => 'karacabey-gross-market',
        'domain' => 'karacabeygrossmarket.com',
    ]);
    $product = Product::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Taze Ekmek',
        'slug' => 'taze-ekmek',
        'price_cents' => 1250,
        'stock_quantity' => 20,
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->put('/admin/products/'.$product->id, [
            'name' => 'Taze Ekmek',
            'slug' => 'taze-ekmek',
            'price_cents' => 1500,
            'stock_quantity' => 35,
            'is_active' => '1',
        ])
        ->assertRedirect('/admin/products');

    expect($product->fresh()->price_cents)->toBe(1500)
        ->and($product->fresh()->stock_quantity)->toBe(35);
});

it('records failed admin login attempts and blocks repeated ips', function (): void {
    config([
        'admin_security.max_failed_attempts' => 2,
        'admin_security.block_minutes' => 20,
    ]);

    $server = [
        'REMOTE_ADDR' => '203.0.113.44',
        'HTTP_USER_AGENT' => 'feature-test',
    ];
    $payload = [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ];

    $this->withServerVariables($server)
        ->post('/admin/login', $payload)
        ->assertSessionHasErrors('email');

    expect(AdminAuthLog::query()
        ->where('ip_address', '203.0.113.44')
        ->where('event_type', 'login_attempt')
        ->where('status', 'failed')
        ->exists())->toBeTrue();

    $this->withServerVariables($server)
        ->post('/admin/login', $payload)
        ->assertSessionHasErrors('email');

    expect(AdminAuthLog::query()
        ->where('ip_address', '203.0.113.44')
        ->where('event_type', 'login_attempt')
        ->where('status', 'blocked')
        ->whereNotNull('blocked_until')
        ->exists())->toBeTrue();

    $this->withServerVariables($server)
        ->get('/admin/login')
        ->assertStatus(423)
        ->assertSee('Auth2');
});

it('maps decoy admin auth2 routes and exposes logs in the admin panel', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->withServerVariables([
        'REMOTE_ADDR' => '198.51.100.19',
        'HTTP_USER_AGENT' => 'feature-test',
    ])
        ->get('/admin/oauth2/authorize?client_id=kgm-console')
        ->assertOk()
        ->assertSee('Auth2');

    expect(AdminAuthLog::query()
        ->where('ip_address', '198.51.100.19')
        ->where('event_type', 'decoy_oauth_view')
        ->exists())->toBeTrue();

    $this->actingAs($admin)
        ->get('/admin/auth-logs')
        ->assertOk()
        ->assertSeeText('Auth Log')
        ->assertSeeText('198.51.100.19');
});
