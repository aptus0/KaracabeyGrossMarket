<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ErkurAnalyticsService;
use App\Support\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ErpSayimController extends Controller
{
    public function __construct(private readonly ErkurAnalyticsService $erkur) {}

    public function index(Request $request, TenantResolver $tenants): View
    {
        $data = $this->erkur->getSayimListesi();

        return view('admin.erp.sayim', [
            'sayimlar' => $data['sayimlar'],
            'ozet'     => $data['ozet'],
        ]);
    }
}
