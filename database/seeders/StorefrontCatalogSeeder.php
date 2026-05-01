<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorefrontCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->where('slug', 'karacabey-gross-market')->first();

        if (! $tenant) {
            return;
        }

        $categories = $this->seedCategories($tenant);
        $this->seedProducts($tenant, $categories);
    }

    /**
     * @return array<string, \App\Models\Category>
     */
    private function seedCategories(Tenant $tenant): array
    {
        $imageBase = 'https://images.unsplash.com/';
        $definitions = [
            [
                'name' => 'Temel Gıda',
                'description' => 'Bakliyat, pirinç, bulgur, makarna, un, yağ, şeker ve tuz gibi temel mutfak ürünleri.',
                'image_url' => $imageBase.'photo-1514996937319-344454492b37?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Meyve & Sebze',
                'description' => 'Taze meyveler, sebzeler, yeşillikler ve günlük organik ürünler.',
                'image_url' => $imageBase.'photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Şarküteri',
                'description' => 'Peynir, zeytin, sucuk, salam, sosis ve kahvaltı sofralarına yakışan seçkiler.',
                'image_url' => $imageBase.'photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Et, Tavuk & Balık',
                'description' => 'Kırmızı et, tavuk ürünleri, balık ve dondurulmuş protein ürünleri.',
                'image_url' => $imageBase.'photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Süt & Kahvaltılık',
                'description' => 'Süt, yoğurt, ayran, tereyağı, yumurta, reçel ve bal ile kahvaltı sofraları.',
                'image_url' => $imageBase.'photo-1502741338009-cac2772e18bc?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Atıştırmalık & İçecek',
                'description' => 'Cips, çikolata, bisküvi, kuruyemiş, gazlı içecek ve meyve suyu çeşitleri.',
                'image_url' => $imageBase.'photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Temizlik Ürünleri',
                'description' => 'Deterjan, bulaşık ürünleri, yüzey temizleyiciler ve ev temizliği ihtiyaçları.',
                'image_url' => $imageBase.'photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Kozmetik & Bakım',
                'description' => 'Makyaj, cilt ve saç bakımı, kişisel bakım ve seyahat ürünleri için ayrı bir bölüm.',
                'image_url' => $imageBase.'photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80',
                'children' => [
                    [
                        'name' => 'Makyaj Ürünleri',
                        'description' => 'Ruj, fondöten, maskara, eyeliner, far paleti ve makyaj bazı ürünleri.',
                    ],
                    [
                        'name' => 'Cilt & Saç Bakımı',
                        'description' => 'Yüz kremi, serum, güneş kremi, şampuan, saç kremi ve saç bakım ürünleri.',
                    ],
                    [
                        'name' => 'Kişisel Bakım',
                        'description' => 'Deodorant, duş jeli, parfüm, tıraş ürünleri ve ağız bakım ürünleri.',
                    ],
                    [
                        'name' => 'Çanta & Aksesuar',
                        'description' => 'Kadın çantası, makyaj çantası, organizer ve bakım aksesuarları.',
                    ],
                    [
                        'name' => 'Bavul & Seyahat',
                        'description' => 'Bavul, valiz, kabin boy çanta ve seyahat düzenleyici ürünler.',
                    ],
                ],
            ],
            [
                'name' => 'Züccaciye & Mutfak',
                'description' => 'Tabak, bardak, tencere, tava, saklama kabı ve mutfak gereçleri.',
                'image_url' => $imageBase.'photo-1517705008128-361805f42e86?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'Hırdavat & Ev Gereçleri',
                'description' => 'Ampul, pil, bant, vida, priz ve günlük küçük ev ihtiyaçları.',
                'image_url' => $imageBase.'photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        $categoryMap = [];
        $sortOrder = 10;

        foreach ($definitions as $definition) {
            $slug = Str::slug($definition['name']);
            $category = Category::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $slug],
                [
                    'parent_id' => null,
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'image_url' => $definition['image_url'],
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );

            $categoryMap[$slug] = $category;
            $sortOrder += 10;

            foreach ($definition['children'] ?? [] as $index => $childDefinition) {
                $childSlug = Str::slug($childDefinition['name']);
                $childCategory = Category::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $childSlug],
                    [
                        'parent_id' => $category->id,
                        'name' => $childDefinition['name'],
                        'description' => $childDefinition['description'],
                        'image_url' => null,
                        'sort_order' => ($index + 1) * 10,
                        'is_active' => true,
                    ]
                );

                $categoryMap[$childSlug] = $childCategory;
            }
        }

        return $categoryMap;
    }

    /**
     * @param  array<string, \App\Models\Category>  $categories
     */
    private function seedProducts(Tenant $tenant, array $categories): void
    {
        if (Product::query()->where('tenant_id', $tenant->id)->exists()) {
            return;
        }

        $products = [
            ['name' => 'Osmancık Pirinç 2 KG', 'brand' => 'Yayla', 'category' => 'temel-gida', 'price_cents' => 16490, 'compare_at' => 17990, 'stock' => 64, 'description' => 'Pilavlar ve günlük mutfak kullanımı için ideal Osmancık pirinç.'],
            ['name' => 'Pilavlık Bulgur 1 KG', 'brand' => 'Duru', 'category' => 'temel-gida', 'price_cents' => 6490, 'compare_at' => 7490, 'stock' => 52, 'description' => 'Günlük sofralar için iri taneli pilavlık bulgur.'],
            ['name' => 'Ayçiçek Yağı 5 LT', 'brand' => 'Yudum', 'category' => 'temel-gida', 'price_cents' => 23990, 'compare_at' => 25990, 'stock' => 21, 'description' => 'Yemek ve kızartma kullanımı için ekonomik boy ayçiçek yağı.'],
            ['name' => 'Domates 1 KG', 'brand' => 'Karacabey Taze', 'category' => 'meyve-sebze', 'price_cents' => 3990, 'stock' => 96, 'description' => 'Günlük taze domates, kahvaltı ve yemekler için uygun.'],
            ['name' => 'Salatalık 1 KG', 'brand' => 'Karacabey Taze', 'category' => 'meyve-sebze', 'price_cents' => 3290, 'stock' => 88, 'description' => 'Çıtır ve serinletici salatalık.'],
            ['name' => 'Muz 1 KG', 'brand' => 'İthal', 'category' => 'meyve-sebze', 'price_cents' => 7190, 'stock' => 37, 'description' => 'Tatlı aromalı günlük muz seçkisi.'],
            ['name' => 'Tam Yağlı Beyaz Peynir 700 G', 'brand' => 'Sütaş', 'category' => 'sarkuteri', 'price_cents' => 13990, 'compare_at' => 14990, 'stock' => 33, 'description' => 'Kahvaltı sofraları için tam yağlı beyaz peynir.'],
            ['name' => 'Vakumlu Dana Sucuk 300 G', 'brand' => 'Namet', 'category' => 'sarkuteri', 'price_cents' => 12490, 'stock' => 27, 'description' => 'Kahvaltı ve tostlar için dana sucuk.'],
            ['name' => 'Siyah Zeytin 800 G', 'brand' => 'Marmarabirlik', 'category' => 'sarkuteri', 'price_cents' => 10490, 'stock' => 18, 'description' => 'Sele tipi kahvaltılık siyah zeytin.'],
            ['name' => 'Kasap Dana Kıyma 500 G', 'brand' => 'KGM Kasap', 'category' => 'et-tavuk-balik', 'price_cents' => 18990, 'stock' => 14, 'description' => 'Günlük paketlenmiş dana kıyma.'],
            ['name' => 'Bütün Tavuk 1.5 KG', 'brand' => 'Banvit', 'category' => 'et-tavuk-balik', 'price_cents' => 11990, 'stock' => 26, 'description' => 'Fırın ve tencere yemekleri için bütün tavuk.'],
            ['name' => 'Temizlenmiş Hamsi 500 G', 'brand' => 'KGM Balık', 'category' => 'et-tavuk-balik', 'price_cents' => 8990, 'stock' => 11, 'description' => 'Karadeniz hamsisi, ayıklanmış ve pişirmeye hazır.'],
            ['name' => 'Günlük Süt 1 LT', 'brand' => 'Pınar', 'category' => 'sut-kahvaltilik', 'price_cents' => 4290, 'stock' => 71, 'description' => 'Pastörize günlük süt.'],
            ['name' => 'Köy Yumurtası 15\'li', 'brand' => 'CP', 'category' => 'sut-kahvaltilik', 'price_cents' => 8790, 'stock' => 46, 'description' => 'Kahvaltılar için 15 adet köy yumurtası.'],
            ['name' => 'Süzme Yoğurt 900 G', 'brand' => 'Pınar', 'category' => 'sut-kahvaltilik', 'price_cents' => 6890, 'stock' => 40, 'description' => 'Yoğun kıvamlı süzme yoğurt.'],
            ['name' => 'Patates Cipsi 150 G', 'brand' => 'Ruffles', 'category' => 'atistirmalik-icecek', 'price_cents' => 5590, 'stock' => 63, 'description' => 'Kıtır patates cipsi.'],
            ['name' => 'Kolalı İçecek 1 LT', 'brand' => 'Coca-Cola', 'category' => 'atistirmalik-icecek', 'price_cents' => 3990, 'stock' => 85, 'description' => 'Soğuk servis için gazlı içecek.'],
            ['name' => 'Meyve Suyu Karışık 1 LT', 'brand' => 'Cappy', 'category' => 'atistirmalik-icecek', 'price_cents' => 4690, 'stock' => 34, 'description' => 'Karışık meyveli günlük içecek.'],
            ['name' => 'Çamaşır Deterjanı 6 KG', 'brand' => 'Omo', 'category' => 'temizlik-urunleri', 'price_cents' => 26490, 'compare_at' => 28990, 'stock' => 19, 'description' => 'Beyazlar ve renkliler için toz deterjan.'],
            ['name' => 'Bulaşık Tableti 40\'lı', 'brand' => 'Finish', 'category' => 'temizlik-urunleri', 'price_cents' => 17990, 'stock' => 24, 'description' => 'Makinede etkili temizlik için tablet seti.'],
            ['name' => 'Yüzey Temizleyici 2.5 LT', 'brand' => 'Cif', 'category' => 'temizlik-urunleri', 'price_cents' => 7990, 'stock' => 31, 'description' => 'Çok amaçlı yüzey temizleyici.'],
            ['name' => 'Mat Ruj 01 Nude', 'brand' => 'Pastel', 'category' => 'makyaj-urunleri', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 6290, 'stock' => 28, 'description' => 'Günlük kullanım için mat bitişli ruj.'],
            ['name' => 'Güneş Kremi SPF50', 'brand' => 'Nivea', 'category' => 'cilt-sac-bakimi', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 15490, 'stock' => 17, 'description' => 'Yüksek koruma sağlayan güneş kremi.'],
            ['name' => 'Şampuan 600 ML', 'brand' => 'Elidor', 'category' => 'cilt-sac-bakimi', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 7490, 'stock' => 29, 'description' => 'Günlük saç bakımı için besleyici şampuan.'],
            ['name' => 'Deodorant Sprey 150 ML', 'brand' => 'Rexona', 'category' => 'kisisel-bakim', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 6990, 'stock' => 26, 'description' => 'Gün boyu ferahlık sağlayan deodorant sprey.'],
            ['name' => 'Makyaj Çantası Orta Boy', 'brand' => 'KGM Style', 'category' => 'canta-aksesuar', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 12990, 'stock' => 8, 'description' => 'Günlük kullanım için bölmeli makyaj çantası.'],
            ['name' => 'Kabin Boy Bavul', 'brand' => 'TravelGo', 'category' => 'bavul-seyahat', 'parent_category' => 'kozmetik-bakim', 'price_cents' => 49990, 'stock' => 6, 'description' => 'Kısa seyahatler için dayanıklı kabin boy bavul.'],
            ['name' => 'Cam Saklama Kabı 3\'lü', 'brand' => 'Paşabahçe', 'category' => 'zuccaciye-mutfak', 'price_cents' => 11990, 'stock' => 16, 'description' => 'Mutfakta düzen sağlayan kapaklı saklama kabı seti.'],
            ['name' => 'Çelik Tava 28 CM', 'brand' => 'Korkmaz', 'category' => 'zuccaciye-mutfak', 'price_cents' => 32990, 'stock' => 7, 'description' => 'Geniş yüzeyli çelik tava.'],
            ['name' => 'LED Ampul 12W', 'brand' => 'Philips', 'category' => 'hirdavat-ev-gerecleri', 'price_cents' => 4590, 'stock' => 41, 'description' => 'Uzun ömürlü beyaz ışık LED ampul.'],
            ['name' => 'Çiftli Priz Uzatma', 'brand' => 'Mutlusan', 'category' => 'hirdavat-ev-gerecleri', 'price_cents' => 9490, 'stock' => 14, 'description' => 'Günlük ev kullanımı için güvenli uzatma prizi.'],
        ];

        foreach ($products as $index => $definition) {
            $slug = Str::slug($definition['name']);
            $product = Product::query()->create([
                'tenant_id' => $tenant->id,
                'name' => $definition['name'],
                'slug' => $slug,
                'description' => $definition['description'],
                'brand' => $definition['brand'],
                'barcode' => str_pad((string) (8690000000000 + $index), 13, '0', STR_PAD_LEFT),
                'price_cents' => $definition['price_cents'],
                'compare_at_price_cents' => $definition['compare_at'] ?? null,
                'stock_quantity' => $definition['stock'],
                'image_url' => null,
                'seo' => [
                    'title' => $definition['name'].' | Karacabey Gross Market',
                    'description' => $definition['description'],
                    'sku' => 'KGM-'.$tenant->id.'-'.($index + 1),
                ],
                'metadata' => [
                    'seeded' => true,
                    'category_slug' => $definition['category'],
                ],
                'is_active' => true,
            ]);

            $categoryIds = collect([
                $categories[$definition['category']]->id ?? null,
                isset($definition['parent_category']) ? ($categories[$definition['parent_category']]->id ?? null) : null,
            ])->filter()->values()->all();

            $product->categories()->sync($categoryIds);
        }
    }
}
