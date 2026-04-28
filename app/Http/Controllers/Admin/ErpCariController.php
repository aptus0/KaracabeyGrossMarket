<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ErkurAnalyticsService;
use App\Support\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ErpCariController extends Controller
{
    public function __construct(private readonly ErkurAnalyticsService $erkur) {}

    public function index(Request $request, TenantResolver $tenants): View
    {
        $q    = $request->get('q');
        $tur  = $request->get('tur');
        $data = $this->erkur->getCariListesi(['q' => $q, 'tur' => $tur]);

        return view('admin.erp.cari', [
            'cariler' => $data['cariler'],
            'ozet'    => $data['ozet'],
            'q'       => $q,
            'tur'     => $tur,
        ]);
    }

    public function show(int $id, Request $request): View
    {
        $data   = $this->erkur->getCariDetay($id);

        return view('admin.erp.cari-show', [
            'cari'    => $data['cari'],
            'adresler'=> $data['adresler'],
            'fisler'  => $data['fisler'],
            'ozet'    => $data['ozet'],
            'id'      => $id,
        ]);
    }
}
