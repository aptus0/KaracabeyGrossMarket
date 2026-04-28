<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportErkurProducts extends Command
{
    protected $signature   = 'erkur:import-products
                                {--fresh : Önce mevcut tüm ürünleri sil}
                                {--limit= : Maksimum kaç ürün aktarılsın (test için)}
                                {--chunk=500 : Toplu insert boyutu}';

    protected $description = 'Erkur ERP STOK.csv dosyasından ürünleri MySQL\'e aktarır';

    // ── CSV sütun indeksleri (0-based) ──────────────────────────────────
    // STOK.csv sütun sırası:
    // 0=ID, 1=KOD, 2=STOK_GRUP, 3=AD, 4=STOK_CINSI, 5=STOK_VERGI,
    // 23=AKTIF, 25=WEBDEYAYINLANIRMI, 26=STOK_MARKA, 57=NAME(İngilizce)
    // 18=SON_ALIS_FIYAT

    public function handle(): int
    {
        $stokPath     = base_path('erkur_dump/STOK.csv');
        $birimPath    = base_path('erkur_dump/STOK_STOK_BIRIM.csv');
        $fiyatPath    = base_path('erkur_dump/STOK_STOK_BIRIM_FIYAT.csv');
        $barkodPath   = base_path('erkur_dump/STOK_BARKOD.csv');
        $grupPath     = base_path('erkur_dump/STOK_GRUP.csv');

        foreach ([$stokPath, $birimPath, $fiyatPath] as $path) {
            if (! file_exists($path)) {
                $this->error("Dosya bulunamadı: $path");
                return self::FAILURE;
            }
        }

        $tenant = Tenant::first();
        if (! $tenant) {
            $this->error('Tenant bulunamadı. Önce migrate:fresh çalıştırın.');
            return self::FAILURE;
        }

        // ── 1. Fresh temizlik ────────────────────────────────────────────
        if ($this->option('fresh')) {
            $this->warn('Mevcut ürünler siliniyor...');
            Product::query()->where('tenant_id', $tenant->id)->delete();
            $this->info('Ürünler temizlendi.');
        }

        // ── 2. Fiyat haritası: STOK_STOK_BIRIM.ID → parekende fiyat ────
        $this->info('Fiyat tablosu yükleniyor...');
        $prices = [];   // birimId => fiyat (kuruş)

        $fp = fopen($fiyatPath, 'r');
        $row = 0;
        while (($line = fgetcsv($fp, 0, ',')) !== false) {
            $row++;
            if ($row <= 2) continue; // başlık + tip satırı

            // ID, STOK_STOK_BIRIM, DOVIZ_AD, STOK_FIYAT_AD, FIYAT, KDV_DAHILMI
            $birimId  = (int) $line[1];
            $fiyatAd  = (int) $line[3]; // 1016 = Parekende
            $fiyat    = (float) str_replace(',', '.', $line[4]);

            // Parekende fiyatını önceliklendir, yoksa ilk bulduğunu al
            if ($fiyatAd === 1016 || ! isset($prices[$birimId])) {
                $prices[$birimId] = (int) round($fiyat * 100);
            }
        }
        fclose($fp);
        $this->info('Fiyat kaydı: ' . number_format(count($prices)));

        // ── 3. Birim haritası: STOK.ID → STOK_STOK_BIRIM.ID ────────────
        $this->info('Birim tablosu yükleniyor...');
        $stokBirim = []; // stokId => birimId

        $fp = fopen($birimPath, 'r');
        $row = 0;
        while (($line = fgetcsv($fp, 0, ',')) !== false) {
            $row++;
            if ($row <= 2) continue;

            // ID, STOK, STOK_BIRIM, CARPAN, VARSAYILAN...
            $birimId  = (int) $line[0];
            $stokId   = (int) $line[1];
            $varsayilan = (int) $line[4];

            if ($varsayilan === 1 || ! isset($stokBirim[$stokId])) {
                $stokBirim[$stokId] = $birimId;
            }
        }
        fclose($fp);
        $this->info('Birim kaydı: ' . number_format(count($stokBirim)));

        // ── 4. Barkod haritası: stokId → barkod ────────────────────────
        $this->info('Barkod tablosu yükleniyor...');
        $barkodlar = [];

        if (file_exists($barkodPath)) {
            $fp  = fopen($barkodPath, 'r');
            $row = 0;
            while (($line = fgetcsv($fp, 0, ',')) !== false) {
                $row++;
                if ($row <= 2) continue;

                // ID, STOK_STOK_BIRIM, BARKOD, IC_BARKOD, ...
                $birimId = (int) $line[1];
                $barkod  = trim($line[2]);

                if ($barkod && ! empty($barkod) && ! isset($barkodlar[$birimId])) {
                    $barkodlar[$birimId] = $barkod;
                }
            }
            fclose($fp);
        }
        $this->info('Barkod kaydı: ' . number_format(count($barkodlar)));

        // ── 5. Grup haritası: grupId → kategori adı ─────────────────────
        $this->info('Stok grupları yükleniyor...');
        $gruplar = [];

        if (file_exists($grupPath)) {
            $fp  = fopen($grupPath, 'r');
            $row = 0;
            while (($line = fgetcsv($fp, 0, ',')) !== false) {
                $row++;
                if ($row <= 2) continue;
                // ID, USTID, AD...
                $gruplar[(int) $line[0]] = trim($line[2]);
            }
            fclose($fp);
        }

        // ── 6. Kategorileri oluştur/bul ─────────────────────────────────
        $this->info('Kategoriler hazırlanıyor...');
        $categoryMap = []; // grupId => categoryId

        foreach ($gruplar as $grupId => $grupAd) {
            if (empty($grupAd)) continue;

            $cat = Category::firstOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($grupAd) ?: 'grup-' . $grupId],
                ['name' => $grupAd, 'is_active' => true]
            );
            $categoryMap[$grupId] = $cat->id;
        }
        $this->info('Kategori sayısı: ' . count($categoryMap));

        // ── 7. STOK.csv'yi oku ve aktar ─────────────────────────────────
        $this->info('STOK.csv okunuyor...');

        $fp        = fopen($stokPath, 'r');
        $row       = 0;
        $imported  = 0;
        $skipped   = 0;
        $limit     = $this->option('limit') ? (int) $this->option('limit') : PHP_INT_MAX;
        $chunkSize = (int) ($this->option('chunk') ?: 500);
        $buffer    = [];
        $slugsUsed = [];

        // Mevcut slug'ları hafızaya al (duplicate önleme)
        Product::query()->where('tenant_id', $tenant->id)->select('slug')->chunk(1000, function ($rows) use (&$slugsUsed) {
            foreach ($rows as $r) {
                $slugsUsed[$r->slug] = true;
            }
        });

        $bar = $this->output->createProgressBar($limit === PHP_INT_MAX ? 12000 : $limit);
        $bar->start();

        while (($line = fgetcsv($fp, 0, ',')) !== false) {
            $row++;

            // İlk 2 satır: başlık + tip
            if ($row <= 2) continue;

            if ($imported >= $limit) break;

            // Sütun indeksleri
            $stokId   = (int)   ($line[0]  ?? 0);
            $kod      = trim($line[1]  ?? '');
            $grupId   = (int)   ($line[2]  ?? 0);
            $ad       = trim($line[3]  ?? '');
            $aktif    = (int)   ($line[23] ?? 0);
            $marka    = trim($line[26] ?? '');
            $sonAliFiyat = (float) str_replace(',', '.', $line[18] ?? '0');

            // Sadece aktif ürünler
            if ($aktif !== 1 || empty($ad)) {
                $skipped++;
                continue;
            }

            // Fiyat hesapla
            $birimId    = $stokBirim[$stokId] ?? null;
            $fiyatKurus = 0;

            if ($birimId && isset($prices[$birimId])) {
                $fiyatKurus = $prices[$birimId];
            } elseif ($sonAliFiyat > 0) {
                // Fallback: son alış fiyatı (KDV ekle varsayılan %18)
                $fiyatKurus = (int) round($sonAliFiyat * 1.18 * 100);
            }

            // Barkod
            $barkod = $birimId ? ($barkodlar[$birimId] ?? null) : null;

            // Slug oluştur (benzersiz)
            $baseSlug = Str::slug($ad) ?: 'urun-' . $stokId;
            $slug     = $baseSlug;
            $suffix   = 1;
            while (isset($slugsUsed[$slug])) {
                $slug = $baseSlug . '-' . $suffix++;
            }
            $slugsUsed[$slug] = true;

            $now = now()->toDateTimeString();

            $buffer[] = [
                'tenant_id'              => $tenant->id,
                'name'                   => $ad,
                'slug'                   => $slug,
                'description'            => null,
                'brand'                  => $marka ?: null,
                'barcode'                => $barkod,
                'price_cents'            => $fiyatKurus,
                'compare_at_price_cents' => null,
                'stock_quantity'         => 0,
                'image_url'              => null,
                'seo'                    => json_encode(['erkur_kod' => $kod, 'erkur_id' => $stokId]),
                'metadata'               => json_encode(['erkur_stok_id' => $stokId, 'erkur_grup_id' => $grupId, 'category_id' => $categoryMap[$grupId] ?? null]),
                'is_active'              => true,
                'created_at'             => $now,
                'updated_at'             => $now,
            ];

            $imported++;
            $bar->advance();

            // Toplu insert
            if (count($buffer) >= $chunkSize) {
                DB::table('products')->insertOrIgnore($buffer);
                $buffer = [];
            }
        }

        // Kalan buffer
        if (! empty($buffer)) {
            DB::table('products')->insertOrIgnore($buffer);
        }

        fclose($fp);
        $bar->finish();
        $this->newLine();

        // ── 8. Kategori ilişkilerini kur ────────────────────────────────
        $this->info('Kategori ilişkileri kuruluyor...');

        Product::query()
            ->where('tenant_id', $tenant->id)
            ->whereNotNull('metadata')
            ->chunk(500, function ($products) use ($categoryMap) {
                foreach ($products as $product) {
                    $meta      = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                    $grupId    = $meta['erkur_grup_id'] ?? null;
                    $catId     = $grupId ? ($categoryMap[$grupId] ?? null) : null;

                    if ($catId) {
                        DB::table('category_product')
                            ->insertOrIgnore([
                                'product_id'  => $product->id,
                                'category_id' => $catId,
                            ]);
                    }
                }
            });

        $this->newLine();
        $this->info("✅ Aktarım tamamlandı!");
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['İşlenen satır', number_format($row - 2)],
                ['Aktarılan ürün', number_format($imported)],
                ['Atlanan (aktif değil)', number_format($skipped)],
                ['Kategori', number_format(count($categoryMap))],
            ]
        );

        return self::SUCCESS;
    }
}
