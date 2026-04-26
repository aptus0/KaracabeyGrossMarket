<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('categories')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->trim()->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('barcode', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, TenantResolver $tenants): RedirectResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $this->validated($request);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated = $this->withSeoPayload($validated);

        $product = Product::query()->create($validated + [
            'tenant_id' => $tenant->id,
        ]);

        $product->categories()->sync($request->array('category_ids'));

        return redirect()->route('admin.products.index')->with('status', 'Urun olusturuldu.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product->load('categories'),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated = $this->withSeoPayload($validated);

        $product->update($validated);
        $product->categories()->sync($request->array('category_ids'));

        return redirect()->route('admin.products.index')->with('status', 'Urun guncellendi.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'brand' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'meta_image_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + [
            'is_active' => false,
        ];
    }

    private function withSeoPayload(array $validated): array
    {
        $seo = [
            'title' => $validated['seo_title'] ?? null,
            'description' => $validated['seo_description'] ?? null,
            'image_url' => $validated['meta_image_url'] ?? null,
        ];

        unset($validated['seo_title'], $validated['seo_description'], $validated['meta_image_url']);
        $validated['seo'] = array_filter($seo);

        return $validated;
    }
}
