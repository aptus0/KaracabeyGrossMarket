<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\HomepageBlock;
use App\Models\MarketingSetting;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const URL_PRODUCTS = '/products';

    private const URL_KAMPANYALAR = '/kampanyalar';

    public function run(): void
    {
        $tenant = $this->seedTenant();

        $this->seedPages($tenant);
        $this->seedHomepageBlocks($tenant);
        $this->seedNavigation($tenant);
        $this->seedCampaign($tenant);
        $this->seedMarketingSettings($tenant);
        $this->seedAdmin();
        $this->call(StorefrontCatalogSeeder::class);
    }

    private function seedTenant(): Tenant
    {
        $primaryDomain = (string) config('commerce.primary_domain', 'karacabeygrossmarket.com');

        return Tenant::query()->updateOrCreate(
            ['slug' => 'karacabey-gross-market'],
            [
                'name' => 'Karacabey Gross Market',
                'domain' => $primaryDomain,
                'is_active' => true,
                'settings' => [
                    'storefront_domain' => $primaryDomain,
                    'admin_domain' => 'app.karacabeygrossmarket.com',
                    'api_domain' => 'api.karacabeygrossmarket.com',
                    'cdn_domain' => 'cdn.karacabeygrossmarket.com',
                    'market' => 'Karacabey',
                ],
            ]
        );
    }

    private function seedPages(Tenant $tenant): void
    {
        $suffix = ' | Karacabey Gross Market';

        collect([
            ['title' => 'Hakkimizda',              'slug' => 'hakkimizda',              'group' => 'corporate', 'body' => 'Karacabey Gross Market, Karacabey ve cevresi icin hizli market siparisi sunar.'],
            ['title' => 'Iletisim',                'slug' => 'iletisim',                'group' => 'corporate', 'body' => 'Karacabey Gross Market destek ekibine web sitesi ve mobil uygulama uzerinden ulasabilirsiniz.'],
            ['title' => 'KVKK',                    'slug' => 'kvkk',                    'group' => 'legal',     'body' => 'Kisisel verileriniz yasal mevzuata uygun olarak islenir ve korunur.'],
            ['title' => 'Gizlilik Politikasi',     'slug' => 'gizlilik-politikasi',     'group' => 'legal',     'body' => 'Gizlilik ve veri guvenligi sureclerimiz tum dijital kanallar icin gecerlidir.'],
            ['title' => 'Mesafeli Satis Sozlesmesi', 'slug' => 'mesafeli-satis-sozlesmesi', 'group' => 'legal',   'body' => 'Online siparisleriniz mesafeli satis mevzuati kapsaminda yurutulur.'],
            ['title' => 'Iade ve Degisim',         'slug' => 'iade-ve-degisim',         'group' => 'support',   'body' => 'Iade ve degisim talepleri siparis detaylari uzerinden takip edilir.'],
            ['title' => 'SSS',                     'slug' => 'sss',                     'group' => 'support',   'body' => 'Teslimat, odeme ve hesap islemleri hakkinda sik sorulan sorular.'],
        ])->each(fn (array $page) => Page::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => $page['slug']],
            $page + [
                'tenant_id' => $tenant->id,
                'is_published' => true,
                'published_at' => now(),
                'seo_title' => $page['title'].$suffix,
                'seo_description' => $page['title'].' sayfasi ve Karacabey Gross Market kurumsal bilgileri.',
            ]
        ));
    }

    private function seedHomepageBlocks(Tenant $tenant): void
    {
        HomepageBlock::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'type' => 'campaign', 'title' => 'Haftalik gross firsatlari'],
            [
                'tenant_id' => $tenant->id,
                'subtitle' => 'Temel gida ve gunluk urunlerde avantajli sepetler.',
                'link_url' => self::URL_KAMPANYALAR,
                'link_label' => 'Kampanyalari Gor',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        collect([
            [
                'title' => 'Karacabey Gross Market',
                'subtitle' => 'Toptan fiyatına, güvenle alışveriş. Günlük market ihtiyaçlarınızı hızlı teslimatla kapınıza getirelim.',
                'image_url' => '/assets/kgm-logo-4k.png',
                'link_url' => self::URL_PRODUCTS,
                'link_label' => 'Alışverişe Başla',
                'sort_order' => 1,
            ],
            [
                'title' => 'Haftalık Gross Fırsatları',
                'subtitle' => 'Temel gıda, kahvaltılık ve taze ürünlerde avantajlı sepetler.',
                'image_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=85',
                'link_url' => self::URL_KAMPANYALAR,
                'link_label' => 'Kampanyaları Gör',
                'sort_order' => 2,
            ],
        ])->each(fn (array $block) => HomepageBlock::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'type' => 'carousel_slide', 'title' => $block['title']],
            $block + ['tenant_id' => $tenant->id, 'type' => 'carousel_slide', 'is_active' => true]
        ));
    }

    private function seedNavigation(Tenant $tenant): void
    {
        $p = self::URL_PRODUCTS;
        $k = self::URL_KAMPANYALAR;

        collect([
            ['placement' => 'top',             'label' => 'Kargo Takip',    'url' => '/cargo-tracking',          'icon' => 'package-search', 'sort_order' => 10],
            ['placement' => 'top',             'label' => 'Adresim',        'url' => '/addresses',               'icon' => 'map-pin',        'sort_order' => 20],
            ['placement' => 'header',          'label' => 'Ürünler',        'url' => $p,                         'icon' => 'grid',           'sort_order' => 10],
            ['placement' => 'header',          'label' => 'Kampanyalar',    'url' => $k,                         'icon' => 'tag',            'sort_order' => 20],
            ['placement' => 'category',        'label' => 'Tüm Ürünler',    'url' => $p,                         'icon' => 'grid',           'sort_order' => 10],
            ['placement' => 'footer_primary',  'label' => 'Ürünler',        'url' => $p,                         'icon' => 'grid',           'sort_order' => 10],
            ['placement' => 'footer_primary',  'label' => 'Kampanyalar',    'url' => $k,                         'icon' => 'tag',            'sort_order' => 20],
            ['placement' => 'footer_primary',  'label' => 'Sepet',          'url' => '/checkout',                'icon' => 'cart',           'sort_order' => 30],
            ['placement' => 'footer_corporate', 'label' => 'Hakkımızda',     'url' => '/kurumsal/hakkimizda',     'icon' => 'file-text',      'sort_order' => 10],
            ['placement' => 'footer_corporate', 'label' => 'İletişim',       'url' => '/kurumsal/iletisim',       'icon' => 'phone',          'sort_order' => 20],
            ['placement' => 'footer_corporate', 'label' => 'KVKK',           'url' => '/kurumsal/kvkk',           'icon' => 'shield',         'sort_order' => 30],
            ['placement' => 'footer_support',  'label' => 'İade ve Değişim', 'url' => '/kurumsal/iade-ve-degisim', 'icon' => 'package-search', 'sort_order' => 10],
            ['placement' => 'footer_support',  'label' => 'SSS',            'url' => '/kurumsal/sss',            'icon' => 'file-text',      'sort_order' => 20],
            ['placement' => 'footer_account',  'label' => 'Hesabım',        'url' => '/account',                 'icon' => 'user',           'sort_order' => 10],
            ['placement' => 'footer_account',  'label' => 'Favoriler',      'url' => '/favorites',               'icon' => 'heart',          'sort_order' => 20],
        ])->each(fn (array $item) => NavigationItem::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'placement' => $item['placement'], 'url' => $item['url']],
            $item + ['tenant_id' => $tenant->id, 'is_active' => true]
        ));
    }

    private function seedCampaign(Tenant $tenant): void
    {
        $campaign = Campaign::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'haftalik-gross-firsatlari'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Haftalik Gross Firsatlari',
                'description' => 'Karacabey Gross Market haftalik avantajli urunleri.',
                'discount_type' => 'fixed',
                'discount_value' => 2500,
                'is_active' => true,
                'seo' => [
                    'title' => 'Haftalik Gross Firsatlari | Karacabey Gross Market',
                    'description' => 'Karacabey Gross Market kampanya ve kupon firsatlari.',
                ],
            ]
        );

        $campaign->coupons()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'KGM25'],
            [
                'tenant_id' => $tenant->id,
                'discount_type' => 'fixed',
                'discount_value' => 2500,
                'minimum_order_cents' => 25000,
                'usage_limit' => 1000,
                'is_active' => true,
            ]
        );
    }

    private function seedMarketingSettings(Tenant $tenant): void
    {
        MarketingSetting::query()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'google_analytics_id' => null,
                'google_ads_id' => null,
                'google_ads_conversion_label' => null,
                'google_site_verification' => null,
                'meta_pixel_id' => null,
            ]
        );
    }

    private function seedAdmin(): void
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@karacabeygrossmarket.com']);

        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }

        $admin->name = 'KGM Admin';
        $admin->is_admin = true;
        $admin->save();
    }
}
