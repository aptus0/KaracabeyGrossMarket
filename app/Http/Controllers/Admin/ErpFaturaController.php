<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ErkurAnalyticsService;
use App\Services\ErpFaturaSyncService;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ErpFaturaController extends Controller
{
    public function __construct(
        private readonly ErkurAnalyticsService $erkur,
        private readonly ErpFaturaSyncService  $syncSvc,
    ) {}

    public function index(Request $request, TenantResolver $tenants): View
    {
        $filters = [
            'tarih_baslangic' => $request->get('baslangic'),
            'tarih_bitis'     => $request->get('bitis'),
            'tip'             => $request->get('tip'),
            'durum'           => $request->get('durum'),
        ];

        $data = $this->erkur->getFaturalar($filters);

        return view('admin.erp.fatura', [
            'faturalar' => $data['faturalar'],
            'ozet'      => $data['ozet'],
            'filters'   => $filters,
        ]);
    }

    public function show(int $id, Request $request): View
    {
        $data     = $this->erkur->getFaturalar([]);
        $fatura   = collect($data['faturalar'])->firstWhere('id', (string) $id);
        $satirlar = $this->syncSvc->getFaturaDetay($id);

        return view('admin.erp.fatura-show', [
            'fatura'   => $fatura,
            'satirlar' => $satirlar,
            'fisId'    => $id,
        ]);
    }

    public function sync(int $id, TenantResolver $tenants, Request $request): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $result = $this->syncSvc->syncFromCsv($id, $tenant->id);

        $msg = "Senkronizasyon tamamlandı: {$result['new']} yeni, {$result['updated']} güncellenen, {$result['stock_updated']} stok düzeltilen ürün.";
        if (! empty($result['errors'])) {
            $msg .= ' Hata: ' . implode(', ', $result['errors']);
        }

        return redirect()->route('admin.erp.fatura.show', $id)->with('status', $msg);
    }
}
