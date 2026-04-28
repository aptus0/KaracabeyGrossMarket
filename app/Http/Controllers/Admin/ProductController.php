<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('categories')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%' . $request->string('q')->trim()->toString() . '%';
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

        $validated['slug'] = $this->generateSlug($validated['slug'], $validated['name']);
        $validated['image_url'] = $this->handleImageUpload($request, $validated['image_url'] ?? null);
        $validated = $this->withSeoPayload($validated);

        $product = Product::query()->create($validated + [
            'tenant_id' => $tenant->id,
        ]);

        $product->categories()->sync($request->array('category_ids'));

        return redirect()->route('admin.products.index')
            ->with('status', 'Ürün oluşturuldu.');
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

        $validated['slug'] = $this->generateSlug($validated['slug'], $validated['name'], $product);
        $validated['image_url'] = $this->handleImageUpload($request, $validated['image_url'] ?? $product->image_url, $product);
        $validated = $this->withSeoPayload($validated);

        $product->update($validated);
        $product->categories()->sync($request->array('category_ids'));

        return redirect()->route('admin.products.index')
            ->with('status', 'Ürün güncellendi.');
    }

    /**
     * Güvenli slug oluşturur (benzersizlik kontrolü ile)
     */
    private function generateSlug(?string $slug, string $name, ?Product $product = null): string
    {
        $baseSlug = $slug ? Str::slug($slug) : Str::slug($name);

        $query = Product::query()->where('slug', $baseSlug);

        if ($product) {
            $query->where('id', '!=', $product->id);
        }

        if (!$query->exists()) {
            return $baseSlug;
        }

        $counter = 1;
        do {
            $newSlug = $baseSlug . '-' . $counter;
            $exists = Product::query()
                ->where('slug', $newSlug)
                ->when($product, fn ($q) => $q->where('id', '!=', $product->id))
                ->exists();
            $counter++;
        } while ($exists);

        return $newSlug;
    }

    /**
     * Güvenli görsel yükleme işlemi
     */
    private function handleImageUpload(Request $request, ?string $fallback, ?Product $product = null): ?string
    {
        if (!$request->hasFile('image_file')) {
            return $fallback;
        }

        $file = $request->file('image_file');

        if (!$file->isValid()) {
            return $fallback;
        }

        // Dosya türü doğrulama
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            return $fallback;
        }

        // Dosya uzantısı doğrulama
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $fallback;
        }

        // Dosya boyutu kontrolü (4MB)
        if ($file->getSize() > 4 * 1024 * 1024) {
            return $fallback;
        }

        // Eski görseli sil (güncelleme durumunda)
        if ($product && $product->image_url) {
            $this->deleteOldImage($product->image_url);
        }

        // Güvenli dosya adı oluştur
        $fileName = Str::uuid() . '.' . $extension;

        // Dosyayı yükle
        $path = $file->storeAs('products', $fileName, 'public');

        if (!$path) {
            return $fallback;
        }

        return asset('storage/' . $path);
    }

    /**
     * Eski görseli storage'dan siler
     */
    private function deleteOldImage(string $imageUrl): void
    {
        try {
            $path = str_replace(asset('storage/'), '', $imageUrl);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // Loglama yapılabilir
        }
    }

    /**
     * Form validasyon kuralları
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'brand' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'meta_image_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]) + [
            'is_active' => false,
        ];
    }

    /**
     * SEO verilerini JSON olarak hazırlar
     */
    private function withSeoPayload(array $validated): array
    {
        $seo = [
            'title' => $validated['seo_title'] ?? null,
            'description' => $validated['seo_description'] ?? null,
            'image_url' => $validated['meta_image_url'] ?? null,
        ];

        unset(
            $validated['seo_title'],
            $validated['seo_description'],
            $validated['meta_image_url'],
            $validated['image_file'],
            $validated['category_ids']
        );

        $validated['seo'] = array_filter($seo);

        return $validated;
    }
}