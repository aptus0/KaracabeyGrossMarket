<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ErkurAnalyticsService;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PDO;
use PDOException;
use Throwable;

class ErpImportController extends Controller
{
    public function __construct(
        private readonly ErkurAnalyticsService $erkur,
    ) {}

    public function index(Request $request, TenantResolver $tenants): View
    {
        $tenant  = $tenants->resolve($request);
        $history = Cache::get("erp_import_history:{$tenant->id}", []);

        return view('admin.erp.import', [
            'history'     => $history,
            'lastImport'  => Cache::get("erp_last_import:{$tenant->id}"),
            'importRunning' => Cache::has("erp_import_running:{$tenant->id}"),
        ]);
    }

    /**
     * MSSQL bağlantısını test eder (AJAX)
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host'     => ['required', 'string', 'max:255'],
            'port'     => ['nullable', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $pdo = $this->makePdo($validated);
            $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM STOK WHERE AKTIF=1");
            $count = $stmt->fetchColumn();

            return response()->json([
                'success' => true,
                'message' => "Bağlantı başarılı! STOK tablosunda {$count} aktif kayıt bulundu.",
                'count'   => (int) $count,
            ]);
        } catch (PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * İmport işlemini başlatır (senkron — büyük veri için progress polling ile)
     */
    public function import(Request $request, TenantResolver $tenants): JsonResponse
    {
        $validated = $request->validate([
            'host'     => ['required', 'string', 'max:255'],
            'port'     => ['nullable', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'fresh'    => ['boolean'],
        ]);

        $tenant = $tenants->resolve($request);
        $runKey = "erp_import_running:{$tenant->id}";

        if (Cache::has($runKey)) {
            return response()->json(['success' => false, 'message' => 'İmport zaten çalışıyor.'], 429);
        }

        Cache::put($runKey, true, now()->addMinutes(30));
        Cache::put("erp_import_progress:{$tenant->id}", ['step' => 'Başlatılıyor...', 'pct' => 0]);

        try {
            $result = $this->runImport($validated, $tenant->id, (bool) ($validated['fresh'] ?? false));

            $history   = Cache::get("erp_import_history:{$tenant->id}", []);
            $historyEntry = [
                'date'      => now()->format('d.m.Y H:i'),
                'imported'  => $result['imported'],
                'skipped'   => $result['skipped'],
                'host'      => $validated['host'],
            ];
            array_unshift($history, $historyEntry);
            $history = array_slice($history, 0, 10);

            Cache::put("erp_import_history:{$tenant->id}", $history, now()->addDays(30));
            Cache::put("erp_last_import:{$tenant->id}", $historyEntry, now()->addDays(30));
            Cache::forget($runKey);

            return response()->json([
                'success' => true,
                'message' => "Aktarım tamamlandı! {$result['imported']} ürün aktarıldı.",
                'result'  => $result,
            ]);
        } catch (Throwable $e) {
            Cache::forget($runKey);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        return response()->json([
            'running'  => Cache::has("erp_import_running:{$tenant->id}"),
            'progress' => Cache::get("erp_import_progress:{$tenant->id}", ['step' => '-', 'pct' => 0]),
        ]);
    }

    // ── Private Helpers ──────────────────────────────────────────────────

    private function makePdo(array $config): PDO
    {
        $host = $config['host'];
        $port = $config['port'] ?? 1433;
        $db   = $config['database'];
        $user = $config['username'];
        $pass = $config['password'] ?? '';

        $dsn = "sqlsrv:Server={$host},{$port};Database={$db};TrustServerCertificate=1;Encrypt=0";

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_TIMEOUT              => 10,
            PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
            PDO::SQLSRV_ATTR_ENCODING      => PDO::SQLSRV_ENCODING_UTF8,
        ]);
    }

    private function runImport(array $config, int $tenantId, bool $fresh): array
    {
        $pdo = $this->makePdo($config);

        // ── Fiyatları çek ────────────────────────────────────────────────
        Cache::put("erp_import_progress:{$tenantId}", ['step' => 'Fiyatlar çekiliyor...', 'pct' => 10]);

        $prices    = [];
        $birimMap  = [];
        $barcodMap = [];

        $stmt = $pdo->query("
            SELECT ssb.ID as birim_id, ssb.STOK as stok_id, f.FIYAT, f.STOK_FIYAT_AD
            FROM STOK_STOK_BIRIM ssb
            LEFT JOIN STOK_STOK_BIRIM_FIYAT f ON f.STOK_STOK_BIRIM = ssb.ID
            WHERE ssb.VARSAYILAN = 1
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $birimId = (int) $row['birim_id'];
            $stokId  = (int) $row['stok_id'];
            $fiyat   = (float) ($row['FIYAT'] ?? 0);
            $fiyatAd = (int) ($row['STOK_FIYAT_AD'] ?? 0);

            $birimMap[$stokId] = $birimId;

            if ($fiyatAd === 1016 || ! isset($prices[$birimId])) {
                $prices[$birimId] = (int) round($fiyat * 100);
            }
        }

        // ── Barkodları çek ───────────────────────────────────────────────
        Cache::put("erp_import_progress:{$tenantId}", ['step' => 'Barkodlar çekiliyor...', 'pct' => 20]);

        $stmt = $pdo->query("SELECT TOP 1 ID FROM STOK"); // bağlantı testi
        $stmt = $pdo->query("SELECT STOK_STOK_BIRIM, BARKOD FROM STOK_BARKOD WHERE BARKOD IS NOT NULL AND BARKOD != ''");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $birimId = (int) $row['STOK_STOK_BIRIM'];
            if (! isset($barcodMap[$birimId])) {
                $barcodMap[$birimId] = trim($row['BARKOD']);
            }
        }

        // ── Fresh temizlik ───────────────────────────────────────────────
        if ($fresh) {
            Cache::put("erp_import_progress:{$tenantId}", ['step' => 'Mevcut ürünler temizleniyor...', 'pct' => 25]);
            \DB::table('products')->where('tenant_id', $tenantId)->delete();
        }

        // ── STOK'u çek ve aktar ──────────────────────────────────────────
        Cache::put("erp_import_progress:{$tenantId}", ['step' => 'Ürünler çekiliyor...', 'pct' => 30]);

        $stmt = $pdo->query("
            SELECT s.ID, s.KOD, s.STOK_GRUP, s.AD, s.AKTIF, s.STOK_MARKA, s.SON_ALIS_FIYAT
            FROM STOK s
            WHERE s.AKTIF = 1 AND s.AD IS NOT NULL AND s.AD != ''
        ");

        $imported  = 0;
        $skipped   = 0;
        $buffer    = [];
        $chunkSize = 500;
        $slugsUsed = [];
        $now       = now()->toDateTimeString();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total    = count($products);

        foreach ($products as $i => $row) {
            if ($i % 1000 === 0) {
                $pct = 30 + (int) ($i / max($total, 1) * 60);
                Cache::put("erp_import_progress:{$tenantId}", ['step' => "Ürün aktarılıyor ({$i}/{$total})...", 'pct' => $pct]);
            }

            $stokId  = (int) $row['ID'];
            $ad      = trim($row['AD']);
            $marka   = trim($row['STOK_MARKA'] ?? '');
            $grupId  = (int) ($row['STOK_GRUP'] ?? 0);
            $sonAlis = (float) ($row['SON_ALIS_FIYAT'] ?? 0);

            if (empty($ad)) { $skipped++; continue; }

            $birimId    = $birimMap[$stokId] ?? null;
            $fiyatKurus = $birimId ? ($prices[$birimId] ?? 0) : 0;
            if ($fiyatKurus === 0 && $sonAlis > 0) {
                $fiyatKurus = (int) round($sonAlis * 1.18 * 100);
            }
            $barkod = $birimId ? ($barcodMap[$birimId] ?? null) : null;

            $baseSlug = \Str::slug($ad) ?: 'urun-' . $stokId;
            $slug     = $baseSlug;
            $suffix   = 1;
            while (isset($slugsUsed[$slug])) { $slug = $baseSlug . '-' . $suffix++; }
            $slugsUsed[$slug] = true;

            $buffer[] = [
                'tenant_id'   => $tenantId,
                'name'        => $ad,
                'slug'        => $slug,
                'brand'       => $marka ?: null,
                'barcode'     => $barkod,
                'price_cents' => $fiyatKurus,
                'stock_quantity' => 0,
                'seo'         => json_encode(['erkur_kod' => $row['KOD'], 'erkur_id' => $stokId]),
                'metadata'    => json_encode(['erkur_stok_id' => $stokId, 'erkur_grup_id' => $grupId]),
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $imported++;

            if (count($buffer) >= $chunkSize) {
                \DB::table('products')->insertOrIgnore($buffer);
                $buffer = [];
            }
        }

        if (! empty($buffer)) {
            \DB::table('products')->insertOrIgnore($buffer);
        }

        Cache::put("erp_import_progress:{$tenantId}", ['step' => 'Tamamlandı!', 'pct' => 100]);

        return compact('imported', 'skipped', 'total');
    }
}
