<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingSettingController extends Controller
{
    public function edit(Request $request, TenantResolver $tenants): View
    {
        $tenant = $tenants->resolve($request);

        return view('admin.marketing.edit', [
            'setting' => $tenant->marketingSetting()->firstOrNew(),
        ]);
    }

    public function update(Request $request, TenantResolver $tenants): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'google_analytics_id' => ['nullable', 'string', 'max:80'],
            'google_ads_id' => ['nullable', 'string', 'max:80'],
            'google_ads_conversion_label' => ['nullable', 'string', 'max:120'],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'meta_pixel_id' => ['nullable', 'string', 'max:80'],
        ]);

        $tenant->marketingSetting()->updateOrCreate([], $validated);

        return redirect()->route('admin.marketing.edit')->with('status', 'Pazarlama ayarlari guncellendi.');
    }
}
