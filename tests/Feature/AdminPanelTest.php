<?php

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
