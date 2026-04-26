<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $products = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->latest()
            ->paginate((int) $request->integer('per_page', 12));

        return response()->json($products->through(fn (Product $product): array => $this->serialize($product)));
    }

    public function show(Request $request, TenantResolver $tenants, string $slug): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $product = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->where('slug', $slug)
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
        ];
    }
}
