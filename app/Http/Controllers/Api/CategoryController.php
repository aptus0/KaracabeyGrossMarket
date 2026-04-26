<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $categories = Category::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories->map(fn (Category $category): array => $this->serialize($category, includeChildren: true))->values(),
        ]);
    }

    public function show(Request $request, TenantResolver $tenants, string $slug): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $category = Category::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->with([
                'children' => fn ($query) => $query->where('is_active', true),
                'products' => fn ($query) => $query
                    ->whereBelongsTo($tenant)
                    ->where('is_active', true)
                    ->latest()
                    ->limit(12),
            ])
            ->firstOrFail();

        return response()->json([
            'data' => $this->serialize($category, includeChildren: true, includeProducts: true),
        ]);
    }

    private function serialize(Category $category, bool $includeChildren = false, bool $includeProducts = false): array
    {
        $data = [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image_url' => $category->image_url,
            'seo' => $category->seo,
        ];

        if ($includeChildren) {
            $data['children'] = $category->children
                ->map(fn (Category $child): array => $this->serialize($child))
                ->values();
        }

        if ($includeProducts) {
            $data['products'] = $category->products
                ->map(fn (Product $product): array => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price_cents' => $product->price_cents,
                    'price' => $product->formattedPrice(),
                    'image_url' => $product->image_url,
                ])
                ->values();
        }

        return $data;
    }
}
