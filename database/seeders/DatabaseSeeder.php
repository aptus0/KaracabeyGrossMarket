<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\HomepageBlock;
use App\Models\MarketingSetting;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::query()->updateOrCreate([
            'slug' => 'karacabey-gross-market',
        ], [
            'name' => 'Karacabey Gross Market',
            'domain' => 'karacabeygrossmarket.com',
            'is_active' => true,
            'settings' => [
                'admin_domain' => 'app.karacabeygrossmarket.com',
                'market' => 'Karacabey',
            ],
        ]);

        $categories = collect([
            ['name' => 'Sut ve Kahvaltilik', 'slug' => 'sut-ve-kahvaltilik', 'sort_order' => 10],
            ['name' => 'Firin', 'slug' => 'firin', 'sort_order' => 20],
            ['name' => 'Meyve Sebze', 'slug' => 'meyve-sebze', 'sort_order' => 30],
            ['name' => 'Temel Gida', 'slug' => 'temel-gida', 'sort_order' => 40],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::query()->updateOrCreate([
                'tenant_id' => $tenant->id,
                'slug' => $category['slug'],
            ], $category + [
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'seo' => [
                    'title' => $category['name'].' | Karacabey Gross Market',
                    'description' => $category['name'].' urunlerini Karacabey Gross Market ile online siparis edin.',
                ],
            ]),
        ]);

        collect([
            ['name' => 'Gunluk Sut 1 L', 'slug' => 'gunluk-sut-1-l', 'brand' => 'KGM', 'price_cents' => 4490, 'stock_quantity' => 120, 'category' => 'sut-ve-kahvaltilik'],
            ['name' => 'Taze Ekmek', 'slug' => 'taze-ekmek', 'brand' => 'KGM Firin', 'price_cents' => 1250, 'stock_quantity' => 300, 'category' => 'firin'],
            ['name' => 'Karacabey Domates 1 Kg', 'slug' => 'karacabey-domates-1-kg', 'brand' => 'Yerel Uretici', 'price_cents' => 3890, 'stock_quantity' => 80, 'category' => 'meyve-sebze'],
            ['name' => 'Aycicek Yagi 5 L', 'slug' => 'aycicek-yagi-5-l', 'brand' => 'Gross Secim', 'price_cents' => 32990, 'stock_quantity' => 45, 'category' => 'temel-gida'],
        ])->each(function (array $product) use ($tenant, $categories): void {
            $categorySlug = $product['category'];
            unset($product['category']);

            $model = Product::query()->updateOrCreate([
                'tenant_id' => $tenant->id,
                'slug' => $product['slug'],
            ], $product + [
                'tenant_id' => $tenant->id,
                'description' => 'Karacabey Gross Market hizli teslimat urunu.',
                'is_active' => true,
                'seo' => [
                    'title' => $product['name'].' | Karacabey Gross Market',
                    'description' => 'Karacabey Gross Market ile '.$product['name'].' online siparis.',
                ],
            ]);

            $model->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
        });

        collect([
            ['title' => 'Hakkimizda', 'slug' => 'hakkimizda', 'group' => 'corporate', 'body' => 'Karacabey Gross Market, Karacabey ve cevresi icin hizli market siparisi sunar.'],
            ['title' => 'Iletisim', 'slug' => 'iletisim', 'group' => 'corporate', 'body' => 'Karacabey Gross Market destek ekibine web sitesi ve mobil uygulama uzerinden ulasabilirsiniz.'],
            ['title' => 'KVKK', 'slug' => 'kvkk', 'group' => 'legal', 'body' => 'Kisisel verileriniz yasal mevzuata uygun olarak islenir ve korunur.'],
            ['title' => 'Gizlilik Politikasi', 'slug' => 'gizlilik-politikasi', 'group' => 'legal', 'body' => 'Gizlilik ve veri guvenligi sureclerimiz tum dijital kanallar icin gecerlidir.'],
            ['title' => 'Mesafeli Satis Sozlesmesi', 'slug' => 'mesafeli-satis-sozlesmesi', 'group' => 'legal', 'body' => 'Online siparisleriniz mesafeli satis mevzuati kapsaminda yurutulur.'],
            ['title' => 'Iade ve Degisim', 'slug' => 'iade-ve-degisim', 'group' => 'support', 'body' => 'Iade ve degisim talepleri siparis detaylari uzerinden takip edilir.'],
            ['title' => 'SSS', 'slug' => 'sss', 'group' => 'support', 'body' => 'Teslimat, odeme ve hesap islemleri hakkinda sik sorulan sorular.'],
        ])->each(fn (array $page): Page => Page::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'slug' => $page['slug'],
        ], $page + [
            'tenant_id' => $tenant->id,
            'is_published' => true,
            'published_at' => now(),
            'seo_title' => $page['title'].' | Karacabey Gross Market',
            'seo_description' => $page['title'].' sayfasi ve Karacabey Gross Market kurumsal bilgileri.',
        ]));

        HomepageBlock::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'type' => 'campaign',
            'title' => 'Haftalik gross firsatlari',
        ], [
            'tenant_id' => $tenant->id,
            'subtitle' => 'Temel gida ve gunluk urunlerde avantajli sepetler.',
            'link_url' => '/kampanyalar',
            'link_label' => 'Kampanyalari Gor',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        collect([
            [
                'title' => 'Karacabey Gross Market',
                'subtitle' => 'Toptan fiyatına, güvenle alışveriş. Günlük market ihtiyaçlarınızı hızlı teslimatla kapınıza getirelim.',
                'image_url' => '/assets/kgm-logo-4k.png',
                'link_url' => '/products',
                'link_label' => 'Alışverişe Başla',
                'sort_order' => 1,
            ],
            [
                'title' => 'Haftalık Gross Fırsatları',
                'subtitle' => 'Temel gıda, kahvaltılık ve taze ürünlerde avantajlı sepetler.',
                'image_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=85',
                'link_url' => '/kampanyalar',
                'link_label' => 'Kampanyaları Gör',
                'sort_order' => 2,
            ],
        ])->each(fn (array $block): HomepageBlock => HomepageBlock::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'type' => 'carousel_slide',
            'title' => $block['title'],
        ], $block + [
            'tenant_id' => $tenant->id,
            'type' => 'carousel_slide',
            'is_active' => true,
        ]));

        collect([
            ['placement' => 'top', 'label' => 'Kargo Takip', 'url' => '/cargo-tracking', 'icon' => 'package-search', 'sort_order' => 10],
            ['placement' => 'top', 'label' => 'Adresim', 'url' => '/addresses', 'icon' => 'map-pin', 'sort_order' => 20],
            ['placement' => 'header', 'label' => 'Ürünler', 'url' => '/products', 'icon' => 'grid', 'sort_order' => 10],
            ['placement' => 'header', 'label' => 'Kampanyalar', 'url' => '/kampanyalar', 'icon' => 'tag', 'sort_order' => 20],
            ['placement' => 'header', 'label' => 'Kurumsal', 'url' => '/kurumsal/hakkimizda', 'icon' => 'file-text', 'sort_order' => 30],
            ['placement' => 'category', 'label' => 'Süt ve Kahvaltılık', 'url' => '/products?category=sut-ve-kahvaltilik', 'icon' => 'grid', 'sort_order' => 10],
            ['placement' => 'category', 'label' => 'Fırın', 'url' => '/products?category=firin', 'icon' => 'grid', 'sort_order' => 20],
            ['placement' => 'category', 'label' => 'Meyve Sebze', 'url' => '/products?category=meyve-sebze', 'icon' => 'grid', 'sort_order' => 30],
            ['placement' => 'category', 'label' => 'Temel Gıda', 'url' => '/products?category=temel-gida', 'icon' => 'grid', 'sort_order' => 40],
            ['placement' => 'category', 'label' => 'Tüm Ürünler', 'url' => '/products', 'icon' => 'grid', 'sort_order' => 50],
            ['placement' => 'footer_primary', 'label' => 'Ürünler', 'url' => '/products', 'icon' => 'grid', 'sort_order' => 10],
            ['placement' => 'footer_primary', 'label' => 'Kampanyalar', 'url' => '/kampanyalar', 'icon' => 'tag', 'sort_order' => 20],
            ['placement' => 'footer_primary', 'label' => 'Sepet', 'url' => '/checkout', 'icon' => 'cart', 'sort_order' => 30],
            ['placement' => 'footer_corporate', 'label' => 'Hakkımızda', 'url' => '/kurumsal/hakkimizda', 'icon' => 'file-text', 'sort_order' => 10],
            ['placement' => 'footer_corporate', 'label' => 'İletişim', 'url' => '/kurumsal/iletisim', 'icon' => 'phone', 'sort_order' => 20],
            ['placement' => 'footer_corporate', 'label' => 'KVKK', 'url' => '/kurumsal/kvkk', 'icon' => 'shield', 'sort_order' => 30],
            ['placement' => 'footer_support', 'label' => 'İade ve Değişim', 'url' => '/kurumsal/iade-ve-degisim', 'icon' => 'package-search', 'sort_order' => 10],
            ['placement' => 'footer_support', 'label' => 'SSS', 'url' => '/kurumsal/sss', 'icon' => 'file-text', 'sort_order' => 20],
            ['placement' => 'footer_account', 'label' => 'Hesabım', 'url' => '/account', 'icon' => 'user', 'sort_order' => 10],
            ['placement' => 'footer_account', 'label' => 'Favoriler', 'url' => '/favorites', 'icon' => 'heart', 'sort_order' => 20],
        ])->each(fn (array $item): NavigationItem => NavigationItem::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'placement' => $item['placement'],
            'url' => $item['url'],
        ], $item + [
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]));

        $campaign = Campaign::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'slug' => 'haftalik-gross-firsatlari',
        ], [
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
        ]);

        $campaign->coupons()->updateOrCreate([
            'tenant_id' => $tenant->id,
            'code' => 'KGM25',
        ], [
            'tenant_id' => $tenant->id,
            'discount_type' => 'fixed',
            'discount_value' => 2500,
            'minimum_order_cents' => 25000,
            'usage_limit' => 1000,
            'is_active' => true,
        ]);

        MarketingSetting::query()->updateOrCreate([
            'tenant_id' => $tenant->id,
        ], [
            'google_analytics_id' => null,
            'google_ads_id' => null,
            'google_ads_conversion_label' => null,
            'google_site_verification' => null,
            'meta_pixel_id' => null,
        ]);

        $admin = User::query()->firstOrNew([
            'email' => 'admin@karacabeygrossmarket.com',
        ]);

        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }

        $admin->name = 'KGM Admin';
        $admin->is_admin = true;
        $admin->save();
    }
}
