<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Support\TenantResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function show(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $cartToken = $this->cartToken($request);
        $items = $this->cartQuery($request, $tenant->id, $cartToken)
            ->with('product')
            ->latest()
            ->get();

        return response()->json(['data' => $this->serializeCart($items, $request->user() ? null : $cartToken)]);
    }

    public function store(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->whereKey($validated['product_id'])
            ->firstOrFail();

        abort_if($product->stock_quantity < $validated['quantity'], 422, 'Stok yetersiz.');

        $cartToken = $this->cartToken($request);
        $query = $this->cartQuery($request, $tenant->id, $cartToken)->where('product_id', $product->id);
        $item = $query->first();

        if ($item) {
            $quantity = min(99, $item->quantity + (int) $validated['quantity']);
            abort_if($product->stock_quantity < $quantity, 422, 'Stok yetersiz.');
            $item->update(['quantity' => $quantity]);
        } else {
            $item = CartItem::query()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $request->user()?->id,
                'cart_token' => $request->user() ? null : $cartToken,
                'product_id' => $product->id,
                'quantity' => (int) $validated['quantity'],
            ]);
        }

        $items = $this->cartQuery($request, $tenant->id, $cartToken)
            ->with('product')
            ->latest()
            ->get();

        return response()->json(['data' => $this->serializeCart($items, $request->user() ? null : $cartToken)], $item->wasRecentlyCreated ? 201 : 200);
    }

    public function update(Request $request, TenantResolver $tenants, CartItem $cartItem): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $cartToken = $this->cartToken($request, create: false);

        abort_unless($this->ownsCartItem($request, $cartItem, $tenant->id, $cartToken), 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cartItem->loadMissing('product');
        abort_if($cartItem->product->stock_quantity < $validated['quantity'], 422, 'Stok yetersiz.');

        $cartItem->update(['quantity' => (int) $validated['quantity']]);

        $items = $this->cartQuery($request, $tenant->id, $cartToken)
            ->with('product')
            ->latest()
            ->get();

        return response()->json(['data' => $this->serializeCart($items, $request->user() ? null : $cartToken)]);
    }

    public function destroy(Request $request, TenantResolver $tenants, CartItem $cartItem): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $cartToken = $this->cartToken($request, create: false);

        abort_unless($this->ownsCartItem($request, $cartItem, $tenant->id, $cartToken), 403);

        $cartItem->delete();

        $items = $this->cartQuery($request, $tenant->id, $cartToken)
            ->with('product')
            ->latest()
            ->get();

        return response()->json(['data' => $this->serializeCart($items, $request->user() ? null : $cartToken)]);
    }

    public function clear(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $cartToken = $this->cartToken($request, create: false);

        $this->cartQuery($request, $tenant->id, $cartToken)->delete();

        return response()->json(['data' => $this->serializeCart(collect(), $request->user() ? null : $cartToken)]);
    }

    private function cartToken(Request $request, bool $create = true): ?string
    {
        if ($request->user()) {
            return null;
        }

        $token = $request->header('X-Cart-Token') ?: $request->input('cart_token');

        if (! $token && $create) {
            $token = (string) Str::uuid();
        }

        return $token ? Str::limit((string) $token, 64, '') : null;
    }

    private function cartQuery(Request $request, int $tenantId, ?string $cartToken): Builder
    {
        return CartItem::query()
            ->where('tenant_id', $tenantId)
            ->when(
                $request->user(),
                fn (Builder $query) => $query->where('user_id', $request->user()->id),
                fn (Builder $query) => $query->where('cart_token', $cartToken)
            );
    }

    private function ownsCartItem(Request $request, CartItem $cartItem, int $tenantId, ?string $cartToken): bool
    {
        if ($cartItem->tenant_id !== $tenantId) {
            return false;
        }

        if ($request->user()) {
            return $cartItem->user_id === $request->user()->id;
        }

        return $cartToken && hash_equals((string) $cartItem->cart_token, $cartToken);
    }

    private function serializeCart($items, ?string $cartToken): array
    {
        $subtotal = $items->sum(fn (CartItem $item): int => $item->product->price_cents * $item->quantity);

        return [
            'cart_token' => $cartToken,
            'items' => $items->map(fn (CartItem $item): array => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'line_total_cents' => $item->product->price_cents * $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'brand' => $item->product->brand,
                    'price_cents' => $item->product->price_cents,
                    'price' => $item->product->formattedPrice(),
                    'stock_quantity' => $item->product->stock_quantity,
                    'image_url' => $item->product->image_url,
                ],
            ])->values(),
            'subtotal_cents' => $subtotal,
            'total_cents' => $subtotal,
        ];
    }
}
