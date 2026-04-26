<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        return view('admin.campaigns.index', [
            'campaigns' => Campaign::query()->with('coupons')->latest()->paginate(20),
            'coupons' => Coupon::query()->with('campaign')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request, TenantResolver $tenants): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'banner_image_url' => ['nullable', 'url', 'max:500'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount_value' => ['required', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ]);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $seo = [
            'title' => $validated['seo_title'] ?? $validated['name'],
            'description' => $validated['seo_description'] ?? $validated['description'] ?? null,
        ];
        unset($validated['seo_title'], $validated['seo_description']);

        Campaign::query()->create($validated + [
            'tenant_id' => $tenant->id,
            'is_active' => $request->boolean('is_active'),
            'seo' => $seo,
        ]);

        return back()->with('status', 'Kampanya olusturuldu.');
    }

    public function storeCoupon(Request $request, TenantResolver $tenants): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'code' => ['required', 'alpha_dash', 'max:64'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount_value' => ['required', 'integer', 'min:0'],
            'minimum_order_cents' => ['nullable', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['minimum_order_cents'] = $validated['minimum_order_cents'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        Coupon::query()->create($validated + [
            'tenant_id' => $tenant->id,
        ]);

        return back()->with('status', 'Kupon olusturuldu.');
    }
}
