<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request, TenantResolver $tenants): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:96'],
        ]);

        $tenant = $tenants->resolve($request);
        $perPage = (int) ($validated['per_page'] ?? 12);
        $page = (int) ($validated['page'] ?? 1);

        $query = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->with('categories')
            ->orderByRaw('CASE WHEN image_url IS NULL OR image_url = "" THEN 1 ELSE 0 END ASC')
            ->latest('id')
            ->when(! empty($validated['q']), function ($query) use ($validated): void {
                $term = '%'.addcslashes(Str::squish((string) $validated['q']), '\\%_').'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('brand', 'like', $term)
                        ->orWhere('barcode', 'like', $term);
                });
            });

        if (! empty($validated['category'])) {
            $category = Category::query()
                ->whereBelongsTo($tenant)
                ->where('is_active', true)
                ->where('slug', $validated['category'])
                ->first();

            $categoryIds = $category
                ? $category->children()->pluck('id')->push($category->id)->all()
                : [];

            $query->when(
                $category,
                fn ($query) => $query->whereHas(
                    'categories',
                    fn ($query) => $query->whereIn('categories.id', $categoryIds)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            );
        }

        $products = $query
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $products->getCollection()
                ->map(fn (Product $product): array => $this->serialize($product))
                ->values(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ]);
    }

    public function show(Request $request, TenantResolver $tenants, string $slug): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $product = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->with('categories')
            ->firstOrFail();

        return response()->json(['data' => $this->serialize($product)]);
    }

    public function suggest(Request $request, TenantResolver $tenants): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
        ]);

        $tenant = $tenants->resolve($request);
        $term = Str::squish((string) ($validated['q'] ?? ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['data' => []]);
        }

        $likeTerm = '%'.addcslashes($term, '\\%_').'%';

        $products = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->with('categories')
            ->where(function ($query) use ($likeTerm): void {
                $query->where('name', 'like', $likeTerm)
                    ->orWhere('brand', 'like', $likeTerm)
                    ->orWhere('barcode', 'like', $likeTerm);
            })
            ->orderByRaw('case when name like ? then 0 else 1 end', [$likeTerm])
            ->latest()
            ->limit(6)
            ->get();

        return response()->json([
            'data' => $products->map(fn (Product $product): array => $this->serializeSuggestion($product))->values(),
        ]);
    }

    private function serialize(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'brand' => $product->brand,
            'price_cents' => $product->price_cents,
            'price' => $product->formattedPrice(),
            'compare_at_price_cents' => $product->compare_at_price_cents,
            'stock_quantity' => $product->stock_quantity,
            'image_url' => $this->safeImageUrl($product->image_url),
            'seo' => $product->seo,
            'categories' => $product->categories
                ->map(fn (Category $category): array => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ])
                ->values(),
        ];
    }

    private function serializeSuggestion(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand,
            'price' => $product->formattedPrice(),
            'image_url' => $this->safeImageUrl($product->image_url),
            'category' => $product->categories->first()?->name,
        ];
    }

    private function safeImageUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return str_starts_with($url, 'https://') || str_starts_with($url, 'http://') ? $url : null;
    }
}
