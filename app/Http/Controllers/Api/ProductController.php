<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $perPage = min(max((int) $request->integer('per_page', 12), 1), 48);

        $query = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->with('categories')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->trim()->toString().'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('brand', 'like', $term)
                        ->orWhere('barcode', 'like', $term);
                });
            });

        if ($request->filled('category')) {
            $category = Category::query()
                ->whereBelongsTo($tenant)
                ->where('is_active', true)
                ->where('slug', $request->string('category')->toString())
                ->first();

            $query->when(
                $category,
                fn ($query) => $query->whereHas('categories', fn ($query) => $query->whereKey($category->id)),
                fn ($query) => $query->whereRaw('1 = 0')
            );
        }

        $products = $query->latest()->paginate($perPage);

        return response()->json($products->through(fn (Product $product): array => $this->serialize($product)));
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
            'image_url' => $product->image_url,
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
}
