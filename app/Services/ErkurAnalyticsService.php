<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ErkurAnalyticsService
{
    private string $dumpPath;

    public function __construct()
    {
        $this->dumpPath = base_path('erkur_dump');
    }

    /**
     * Finans özeti: toplam alacak, borç, bakiye.
     */
    public function getFinansOzeti(): array
    {
        return Cache::remember('erkur:finans_ozeti', now()->addMinutes(30), function () {
            $detayPath = $this->dumpPath . '/FINANS_DETAY.csv';
            if (! file_exists($detayPath)) {
                return $this->emptyFinansOzeti();
            }

            $toplamAlacak = 0.0;
            $toplamBorc = 0.0;
            $kayitSayisi = 0;

            $handle = fopen($detayPath, 'r');
            if ($handle === false) {
                return $this->emptyFinansOzeti();
            }

            // Skip header rows (first 2 lines: header + separator)
            fgetcsv($handle); // header
            fgetcsv($handle); // separator line (--)

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 8) {
                    continue;
                }
                // FINANS_DETAY: ID, FINANS, FINANS_ISLEM_TURU, DOVIZ_AD, KUR, KART_BORCLU, KART_ALACAKLI, TUTAR
                $tutar = (float) ($row[7] ?? 0);
                $toplamAlacak += $tutar;
                $kayitSayisi++;
            }

            fclose($handle);

            // FINANS.csv for transaction count
            $finansPath = $this->dumpPath . '/FINANS.csv';
            $finansSayisi = max(0, $this->countRows($finansPath) - 2);

            return [
                'toplam_islem' => $finansSayisi,
                'toplam_hareket' => $kayitSayisi,
                'toplam_alacak' => $toplamAlacak,
                'son_tarih' => $this->getLastDate($finansPath, 5),
            ];
        });
    }

    /**
     * POS/Kasa özeti: son satışlar.
     */
    public function getPosOzeti(): array
    {
        return Cache::remember('erkur:pos_ozeti', now()->addMinutes(15), function () {
            $path = $this->dumpPath . '/FIS_POS.csv';
            if (! file_exists($path)) {
                return ['toplam_fis' => 0, 'son_fisler' => []];
            }

            $rows = $this->readCsvToCollection($path, 15);

            // FIS_POS: ID, FIS, MASA, KISI, GUN_NO, KAPANIS_TARIHI, PC_ID, POS_SIPARIS_TIPI, Z_NO, ...
            $sonFisler = $rows->filter(fn ($r) => ! empty($r[5]) && $r[5] !== 'NULL')
                ->map(fn ($r) => [
                    'id' => $r[0] ?? '',
                    'fis' => $r[1] ?? '',
                    'gun_no' => $r[4] ?? '',
                    'kapanis_tarihi' => $r[5] ?? '',
                    'z_no' => $r[8] ?? 'N/A',
                    'platform' => $r[15] ?? 'Pos',
                ])
                ->values()
                ->take(8);

            return [
                'toplam_fis' => $this->countRows($path) - 2,
                'son_fisler' => $sonFisler->all(),
            ];
        });
    }

    /**
     * POS fiş toplamları (genel tutar bilgisi).
     */
    public function getPosTutarOzeti(): array
    {
        return Cache::remember('erkur:pos_tutar', now()->addMinutes(30), function () {
            $path = $this->dumpPath . '/POS_FIS_TOPLAMLAR.csv';
            if (! file_exists($path)) {
                return ['toplam_ciro' => 0.0, 'toplam_kdv' => 0.0, 'ortalama_fis' => 0.0];
            }

            $handle = fopen($path, 'r');
            if ($handle === false) {
                return ['toplam_ciro' => 0.0, 'toplam_kdv' => 0.0, 'ortalama_fis' => 0.0];
            }

            // Header: POS_GECICI, SATIRLAR_TOPLAM, SATIR_ISKONTO_TOPLAM, FIS_ISKONTO_TOPLAM, KDV_TOPLAM, GENEL_TOPLAM
            fgetcsv($handle); // header
            fgetcsv($handle); // separator

            $toplamCiro = 0.0;
            $toplamKdv = 0.0;
            $fisCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 6) {
                    continue;
                }
                $toplamCiro += (float) ($row[5] ?? 0); // GENEL_TOPLAM
                $toplamKdv += (float) ($row[4] ?? 0);  // KDV_TOPLAM
                $fisCount++;
            }
            fclose($handle);

            return [
                'toplam_ciro' => $toplamCiro,
                'toplam_kdv' => $toplamKdv,
                'fis_sayisi' => $fisCount,
                'ortalama_fis' => $fisCount > 0 ? round($toplamCiro / $fisCount, 2) : 0.0,
            ];
        });
    }

    /**
     * E-Fatura listesi: son 10 fatura.
     */
    public function getEFaturalar(int $limit = 10): array
    {
        return Cache::remember("erkur:e_faturalar_{$limit}", now()->addMinutes(20), function () use ($limit) {
            $path = $this->dumpPath . '/E_FATURA.csv';
            if (! file_exists($path)) {
                return [];
            }

            $rows = $this->readCsvToCollection($path, $limit + 5);

            // E_FATURA: ID, UID, E_MAIL, VERGINO, TIP, CARI, TARIH, TUTAR, ENTEGRASYON, KABUL_DURUMU, PDF_ALINDI, UBL_ALINDI, BELGENO, ALIASS
            return $rows->map(fn ($r) => [
                'id' => $r[0] ?? '',
                'vergi_no' => $r[3] ?? '',
                'tip' => $r[4] ?? '',
                'cari_id' => $r[5] ?? '',
                'tarih' => isset($r[6]) ? substr($r[6], 0, 10) : '',
                'tutar' => (float) ($r[7] ?? 0),
                'belgeno' => $r[12] ?? '',
                'kabul' => ($r[9] ?? '0') === '1' ? 'Kabul' : 'Bekliyor',
                'email' => $r[2] ?? '',
            ])->take($limit)->values()->all();
        });
    }

    /**
     * Stok özeti: stok sayısı ve son alış fiyatları yüksek olanlar.
     */
    public function getStokOzeti(): array
    {
        return Cache::remember('erkur:stok_ozeti', now()->addHours(1), function () {
            $path = $this->dumpPath . '/STOK.csv';
            if (! file_exists($path)) {
                return ['toplam' => 0, 'aktif' => 0, 'top_stoklar' => []];
            }

            $rows = $this->readCsvToCollection($path, 500);

            // STOK: ID, KOD, STOK_GRUP, AD, ..., AKTIF(23), ..., SON_ALIS_FIYAT(18), SON_ALIS_TARIHI(19)
            $aktif = $rows->filter(fn ($r) => ($r[23] ?? '0') === '1');

            $topStoklar = $aktif
                ->filter(fn ($r) => (float) ($r[18] ?? 0) > 0)
                ->sortByDesc(fn ($r) => (float) ($r[18] ?? 0))
                ->take(8)
                ->map(fn ($r) => [
                    'kod' => $r[1] ?? '',
                    'ad' => $r[3] ?? '',
                    'son_alis_fiyat' => (float) ($r[18] ?? 0),
                    'son_alis_tarihi' => isset($r[19]) ? substr($r[19], 0, 10) : '',
                ])
                ->values()
                ->all();

            return [
                'toplam' => $rows->count(),
                'aktif' => $aktif->count(),
                'top_stoklar' => $topStoklar,
            ];
        });
    }

    /**
     * Cari özeti: aktif cari sayısı + ilk N kayıt.
     */
    public function getCariOzeti(int $limit = 8): array
    {
        return Cache::remember("erkur:cari_ozeti_{$limit}", now()->addHours(1), function () use ($limit) {
            $path = $this->dumpPath . '/CARI.csv';
            if (! file_exists($path)) {
                return ['toplam' => 0, 'aktif' => 0, 'son_cariler' => []];
            }

            $rows = $this->readCsvToCollection($path, 400);

            // CARI: ID, CARI_TUR, KOD, AD, ..., AKTIF(29)
            $aktif = $rows->filter(fn ($r) => ($r[29] ?? '0') === '1');

            $sonCariler = $aktif->take($limit)
                ->map(fn ($r) => [
                    'kod' => $r[2] ?? '',
                    'ad' => $r[3] ?? '',
                    'tur' => ($r[1] ?? '1') === '1' ? 'Alıcı' : 'Satıcı',
                    'vergi_no' => $r[13] ?? '',
                    'vade' => (int) ($r[7] ?? 0),
                ])
                ->values()
                ->all();

            return [
                'toplam' => $rows->count(),
                'aktif' => $aktif->count(),
                'son_cariler' => $sonCariler,
            ];
        });
    }

    /**
     * Sayım özeti.
     */
    public function getSayimOzeti(): array
    {
        return Cache::remember('erkur:sayim_ozeti', now()->addHours(1), function () {
            $path = $this->dumpPath . '/SAYIM.csv';
            $detayPath = $this->dumpPath . '/SAYIM_DETAY.csv';
            if (! file_exists($path)) {
                return ['toplam_sayim' => 0, 'toplam_satir' => 0];
            }

            return [
                'toplam_sayim' => max(0, $this->countRows($path) - 2),
                'toplam_satir' => max(0, $this->countRows($detayPath) - 2),
            ];
        });
    }

    /**
     * Read CSV into a Collection, skipping header and separator lines.
     */
    private function readCsvToCollection(string $path, int $limit = 100): Collection
    {
        if (! file_exists($path)) {
            return collect();
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return collect();
        }

        fgetcsv($handle); // header
        fgetcsv($handle); // separator line (--)

        $rows = [];
        $count = 0;
        while (($row = fgetcsv($handle)) !== false && $count < $limit) {
            $rows[] = $row;
            $count++;
        }
        fclose($handle);

        return collect($rows);
    }

    /**
     * Count total lines in a CSV file.
     */
    private function countRows(string $path): int
    {
        if (! file_exists($path)) {
            return 0;
        }

        $count = 0;
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return 0;
        }
        while (fgetcsv($handle) !== false) {
            $count++;
        }
        fclose($handle);

        return $count;
    }

    /**
     * Get the last date value from a specific column index.
     */
    private function getLastDate(string $path, int $colIndex): string
    {
        if (! file_exists($path)) {
            return '-';
        }

        $lastDate = '-';
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return '-';
        }

        fgetcsv($handle); // header
        fgetcsv($handle); // separator

        while (($row = fgetcsv($handle)) !== false) {
            if (! empty($row[$colIndex]) && $row[$colIndex] !== 'NULL') {
                $lastDate = substr($row[$colIndex], 0, 10);
            }
        }
        fclose($handle);

        return $lastDate;
    }

    private function emptyFinansOzeti(): array
    {
        return [
            'toplam_islem' => 0,
            'toplam_hareket' => 0,
            'toplam_alacak' => 0,
            'son_tarih' => '-',
        ];
    }
}
