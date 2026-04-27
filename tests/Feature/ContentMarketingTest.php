<?php

use App\Models\Campaign;
use App\Models\HomepageBlock;
use App\Models\MarketingSetting;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function contentTenant(): Tenant
{
    return Tenant::query()->create([
        'name' => 'Karacabey Gross Market',
        'slug' => 'karacabey-gross-market',
        'domain' => 'karacabeygrossmarket.com',
    ]);
}

it('serves published pages, homepage blocks, campaigns and marketing settings over api', function (): void {
    $tenant = contentTenant();

    Page::query()->create([
        'tenant_id' => $tenant->id,
        'title' => 'KVKK',
        'slug' => 'kvkk',
        'group' => 'legal',
        'body' => 'KVKK metni',
        'seo_title' => 'KVKK | Karacabey Gross Market',
        'seo_description' => 'KVKK aciklamasi',
        'is_published' => true,
        'published_at' => now(),
    ]);

    HomepageBlock::query()->create([
        'tenant_id' => $tenant->id,
        'type' => 'campaign',
        'title' => 'Haftalik firsat',
        'subtitle' => 'Avantajli sepetler',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    Campaign::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Haftalik Gross Firsatlari',
        'slug' => 'haftalik-gross-firsatlari',
        'discount_type' => 'fixed',
        'discount_value' => 2500,
        'is_active' => true,
    ]);

    MarketingSetting::query()->create([
        'tenant_id' => $tenant->id,
        'google_analytics_id' => 'G-TEST123',
        'meta_pixel_id' => '123456789',
    ]);

    NavigationItem::query()->create([
        'tenant_id' => $tenant->id,
        'placement' => 'header',
        'label' => 'Urunler',
        'url' => '/products',
        'icon' => 'grid',
        'sort_order' => 10,
        'is_active' => true,
    ]);

    $this->getJson('/api/v1/content/pages/kvkk')
        ->assertOk()
        ->assertJsonPath('data.slug', 'kvkk')
        ->assertJsonPath('data.seo.title', 'KVKK | Karacabey Gross Market');

    $this->getJson('/api/v1/content/homepage')
        ->assertOk()
        ->assertJsonPath('data.blocks.0.title', 'Haftalik firsat')
        ->assertJsonPath('data.campaigns.0.slug', 'haftalik-gross-firsatlari');

    $this->getJson('/api/v1/content/marketing')
        ->assertOk()
        ->assertJsonPath('data.google_analytics_id', 'G-TEST123')
        ->assertJsonPath('data.meta_pixel_id', '123456789');

    $this->getJson('/api/v1/content/navigation')
        ->assertOk()
        ->assertJsonPath('data.header.0.label', 'Urunler')
        ->assertJsonPath('data.header.0.icon', 'grid');
});

it('lets admins manage pages and marketing settings', function (): void {
    contentTenant();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post('/admin/pages', [
            'title' => 'Hakkimizda',
            'slug' => 'hakkimizda',
            'group' => 'corporate',
            'body' => 'Kurumsal metin',
            'seo_title' => 'Hakkimizda | Karacabey Gross Market',
            'seo_description' => 'Kurumsal SEO aciklamasi',
            'is_published' => '1',
        ])
        ->assertRedirect('/admin/pages');

    expect(Page::query()->where('slug', 'hakkimizda')->first()?->is_published)->toBeTrue();

    $this->actingAs($admin)
        ->put('/admin/marketing', [
            'google_analytics_id' => 'G-ADMIN123',
            'google_ads_id' => 'AW-ADMIN123',
            'google_ads_conversion_label' => 'checkout',
            'google_site_verification' => 'verify-token',
            'meta_pixel_id' => '987654321',
        ])
        ->assertRedirect('/admin/marketing');

    expect(MarketingSetting::query()->first()?->google_analytics_id)->toBe('G-ADMIN123')
        ->and(MarketingSetting::query()->first()?->meta_pixel_id)->toBe('987654321');
});

it('lets admins manage header and footer navigation safely', function (): void {
    contentTenant();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post('/admin/navigation', [
            'placement' => 'header',
            'label' => 'Kampanyalar',
            'url' => '/kampanyalar',
            'icon' => 'tag',
            'sort_order' => 20,
            'is_active' => '1',
        ])
        ->assertRedirect('/admin/navigation');

    expect(NavigationItem::query()->where('label', 'Kampanyalar')->first()?->url)->toBe('/kampanyalar');

    $this->actingAs($admin)
        ->post('/admin/navigation', [
            'placement' => 'header',
            'label' => 'XSS',
            'url' => 'javascript:alert(1)',
            'icon' => 'tag',
        ])
        ->assertSessionHasErrors('url');
});
