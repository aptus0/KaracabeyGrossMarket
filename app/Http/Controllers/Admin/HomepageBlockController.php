<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageBlock;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageBlockController extends Controller
{
    public function index(): View
    {
        return view('admin.homepage-blocks.index', [
            'blocks' => HomepageBlock::query()->orderBy('sort_order')->paginate(30),
        ]);
    }

    public function store(Request $request, TenantResolver $tenants): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        HomepageBlock::query()->create($validated + [
            'tenant_id' => $tenant->id,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Ana sayfa blogu eklendi.');
    }
}
