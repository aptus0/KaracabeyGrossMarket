<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        collect([
            ['name' => 'Gunluk Sut 1 L', 'slug' => 'gunluk-sut-1-l', 'brand' => 'KGM', 'price_cents' => 4490, 'stock_quantity' => 120],
            ['name' => 'Taze Ekmek', 'slug' => 'taze-ekmek', 'brand' => 'KGM Firin', 'price_cents' => 1250, 'stock_quantity' => 300],
            ['name' => 'Karacabey Domates 1 Kg', 'slug' => 'karacabey-domates-1-kg', 'brand' => 'Yerel Uretici', 'price_cents' => 3890, 'stock_quantity' => 80],
            ['name' => 'Aycicek Yagi 5 L', 'slug' => 'aycicek-yagi-5-l', 'brand' => 'Gross Secim', 'price_cents' => 32990, 'stock_quantity' => 45],
        ])->each(fn (array $product): Product => Product::query()->updateOrCreate([
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
        ]));

        User::factory()->create([
            'name' => 'KGM Admin',
            'email' => 'admin@karacabeygrossmarket.com',
        ]);
    }
}
