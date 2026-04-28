<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\CartCoupon;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Paytr\PaytrClient;
use App\Support\CouponSupport;
use App\Support\TenantResolver;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutController extends Controller
{
    public function store(Request $request, TenantResolver $tenants, PaytrClient $paytr): JsonResponse
    {
        $validated = $request->validate([
            'customer.name' => ['required', 'string', 'max:60'],
            'customer.email' => ['required', 'email', 'max:100'],
            'customer.phone' => ['required', 'string', 'max:20'],
            'shipping.city' => ['nullable', 'string', 'max:120'],
            'shipping.district' => ['nullable', 'string', 'max:120'],
            'shipping.address' => ['required', 'string', 'max:400'],
            'cart_token' => ['nullable', 'string', 'max:64'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:99'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
            'checkout_key' => ['nullable', 'string', 'max:120'],
        ]);

        $tenant = $tenants->resolve($request);
        $user = Auth::guard('api')->user();
        $cartToken = $validated['cart_token'] ?? $request->header('X-Cart-Token');
        $checkoutItems = $this->checkoutItems($validated, $tenant->id, $user, $cartToken);

        abort_if($checkoutItems->isEmpty(), 422, 'Sepet bos.');

        $couponCode = $this->couponSupport()->normalizeCode($validated['coupon_code'] ?? '');
        $checkoutKey = $this->resolveCheckoutKey($request, $validated, $tenant->id, $user, $cartToken, $checkoutItems, $couponCode);
        $existingCheckout = $this->existingCheckoutPayload($tenant->id, $checkoutKey);

        if ($existingCheckout) {
            return response()->json(['data' => $existingCheckout]);
        }

        $lockKey = "checkout:lock:{$tenant->id}:{$checkoutKey}";

        try {
            return Cache::lock($lockKey, 15)->block(5, function () use ($validated, $tenant, $user, $cartToken, $checkoutItems, $couponCode, $checkoutKey, $paytr, $request): JsonResponse {
                $existingCheckout = $this->existingCheckoutPayload($tenant->id, $checkoutKey);

                if ($existingCheckout) {
                    return response()->json(['data' => $existingCheckout]);
                }

                /** @var Order $order */
                $order = DB::transaction(function () use ($validated, $tenant, $user, $cartToken, $checkoutItems, $couponCode, $checkoutKey): Order {
                    $products = Product::query()
                        ->whereBelongsTo($tenant)
                        ->where('is_active', true)
                        ->whereIn('id', $checkoutItems->pluck('product_id')->all())
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                $merchantOid = $this->makeMerchantOid();
                $checkoutRef = $this->makeCheckoutRef();

                $order = Order::query()->create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $user?->id,
                    'merchant_oid' => $merchantOid,
                    'checkout_ref' => $checkoutRef,
                    'status' => OrderStatus::AwaitingPayment,
                    'currency' => config('paytr.currency', 'TL'),
                    'subtotal_cents' => 0,
                    'shipping_cents' => 0,
                    'discount_cents' => 0,
                    'total_cents' => 0,
                    'customer_name' => $validated['customer']['name'],
                    'customer_email' => $validated['customer']['email'],
                    'customer_phone' => $validated['customer']['phone'],
                    'shipping_city' => $validated['shipping']['city'] ?? null,
                    'shipping_district' => $validated['shipping']['district'] ?? null,
                    'shipping_address' => $validated['shipping']['address'],
                    'metadata' => [
                        'source' => 'api_checkout',
                        'cart_token' => $cartToken,
                        'checkout_key' => $checkoutKey,
                        'coupon_code' => null,
                        'stock_reserved' => true,
                        'stock_released' => false,
                    ],
                ]);

                $subtotal = $this->buildOrderItems($order, $checkoutItems, $products);
                $resolvedCouponCode = $couponCode !== ''
                    ? $couponCode
                    : $this->resolveCartCouponCode($tenant->id, $user, $cartToken);
                $discountCents = $this->resolveCouponDiscount($subtotal, $resolvedCouponCode, $tenant->id);
                $totalCents = max(0, $subtotal - $discountCents);
                $metadata = $order->metadata ?? [];
                $metadata['coupon_code'] = $resolvedCouponCode !== '' ? $resolvedCouponCode : null;

                $order->update([
                    'subtotal_cents' => $subtotal,
                    'discount_cents' => $discountCents,
                    'total_cents' => $totalCents,
                    'metadata' => $metadata,
                ]);

                $order->payment()->create([
                    'provider' => 'paytr',
                    'merchant_oid' => $merchantOid,
                    'status' => PaymentStatus::Pending,
                    'amount_cents' => $totalCents,
                    'currency' => config('paytr.currency', 'TL'),
                ]);

                return $order->load('items', 'payment');
            });

            try {
                $iframe = $paytr->getIframeToken($order, $request->ip());
            } catch (RuntimeException $exception) {
                $this->releaseReservedStock($order);

                $order->payment->update([
                    'status' => PaymentStatus::Failed,
                    'failed_reason_msg' => $exception->getMessage(),
                ]);

                return response()->json([
                    'message' => 'Odeme oturumu baslatilamadi.',
                    'reason' => $exception->getMessage(),
                ], 502);
            }

            $order->payment->update(['provider_token' => $iframe['token']]);
            $payload = $this->serializeCheckoutResponse($order, $iframe['token'], $iframe['iframe_src']);

                Cache::put($this->checkoutPayloadCacheKey($tenant->id, $checkoutKey), $payload, now()->addMinutes(15));

                return response()->json([
                    'data' => $payload,
                ], 201);
            });
        } catch (LockTimeoutException) {
            return response()->json([
                'message' => 'Checkout istegi hala isleniyor. Lutfen birkac saniye sonra tekrar deneyin.',
            ], 429);
        }
    }

    /**
     * @param  Collection<int, array{product_id: int, quantity: int}>  $checkoutItems
     * @param  \Illuminate\Database\Eloquent\Collection<int, Product>  $products
     */
    private function buildOrderItems(Order $order, Collection $checkoutItems, \Illuminate\Database\Eloquent\Collection $products): int
    {
        $subtotal = 0;

        foreach ($checkoutItems as $item) {
            $product = $products->get($item['product_id']);

            abort_if(! $product, 422, 'Sepette gecersiz urun var.');
            abort_if($product->stock_quantity < $item['quantity'], 422, $product->name.' icin stok yetersiz.');

            $lineTotal = $product->price_cents * $item['quantity'];
            $subtotal += $lineTotal;
            $product->decrement('stock_quantity', $item['quantity']);

            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price_cents' => $product->price_cents,
                'quantity' => $item['quantity'],
                'line_total_cents' => $lineTotal,
            ]);
        }

        return $subtotal;
    }

    private function resolveCouponDiscount(int $subtotal, string $couponCode, int $tenantId): int
    {
        if ($couponCode === '') {
            return 0;
        }

        $support = $this->couponSupport();
        $coupon = $support->findActiveCoupon($tenantId, $couponCode, lockForUpdate: true);

        if ($support->invalidReason($coupon, $subtotal) !== null) {
            return 0;
        }

        $discountCents = $support->calculateDiscount($coupon, $subtotal);
        $coupon->increment('used_count');

        return $discountCents;
    }

    private function resolveCartCouponCode(int $tenantId, ?User $user, ?string $cartToken): string
    {
        $cartCoupon = CartCoupon::query()
            ->with('coupon')
            ->where('tenant_id', $tenantId)
            ->when(
                $user,
                fn ($query) => $query->where('user_id', $user->id),
                fn ($query) => $query->where('cart_token', $cartToken)
            )
            ->lockForUpdate()
            ->first();

        return $cartCoupon?->coupon?->code ?? '';
    }

    /**
     * @return Collection<int, array{product_id: int, quantity: int}>
     */
    private function checkoutItems(array $validated, int $tenantId, ?User $user, ?string $cartToken): Collection
    {
        if (! empty($validated['items'])) {
            return collect($validated['items'])
                ->map(fn (array $item): array => [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ])
                ->groupBy('product_id')
                ->map(fn (Collection $items, int $productId): array => [
                    'product_id' => $productId,
                    'quantity' => $items->sum('quantity'),
                ])
                ->values();
        }

        $cartItems = CartItem::query()
            ->where('tenant_id', $tenantId)
            ->when(
                $user,
                fn ($query) => $query->where('user_id', $user->id),
                fn ($query) => $query->where('cart_token', $cartToken)
            )
            ->get();

        return $cartItems
            ->map(fn (CartItem $item): array => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ])
            ->values();
    }

    private function releaseReservedStock(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $freshOrder = $order->fresh('items');
            $metadata = $freshOrder->metadata ?? [];

            if (! ($metadata['stock_reserved'] ?? false) || ($metadata['stock_released'] ?? false)) {
                return;
            }

            /** @var EloquentCollection<int, OrderItem> $items */
            $items = $freshOrder->items;

            foreach ($items as $item) {
                if ($item->product_id) {
                    Product::query()->whereKey($item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            $metadata['stock_released'] = true;
            $freshOrder->update(['metadata' => $metadata]);
        });
    }

    private function makeMerchantOid(): string
    {
        do {
            $oid = 'KGM'.now()->format('ymdHis').Str::upper(Str::random(10));
        } while (Order::query()->where('merchant_oid', $oid)->exists());

        return $oid;
    }

    private function makeCheckoutRef(): string
    {
        do {
            $ref = Str::lower(Str::random(32));
        } while (Order::query()->where('checkout_ref', $ref)->exists());

        return $ref;
    }

    private function couponSupport(): CouponSupport
    {
        return app(CouponSupport::class);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  Collection<int, array{product_id: int, quantity: int}>  $checkoutItems
     */
    private function resolveCheckoutKey(Request $request, array $validated, int $tenantId, ?User $user, ?string $cartToken, Collection $checkoutItems, string $couponCode): string
    {
        $providedKey = trim((string) ($request->header('X-Checkout-Key') ?: ($validated['checkout_key'] ?? '')));

        if ($providedKey !== '') {
            return hash('sha256', $providedKey);
        }

        return hash('sha256', json_encode([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'cart_token' => $cartToken,
            'coupon_code' => $couponCode,
            'customer' => [
                'name' => $validated['customer']['name'],
                'email' => mb_strtolower((string) $validated['customer']['email']),
                'phone' => preg_replace('/\D+/', '', (string) $validated['customer']['phone']),
            ],
            'shipping' => [
                'city' => $validated['shipping']['city'] ?? null,
                'district' => $validated['shipping']['district'] ?? null,
                'address' => $validated['shipping']['address'],
            ],
            'items' => $checkoutItems
                ->sortBy('product_id')
                ->values()
                ->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function existingCheckoutPayload(int $tenantId, string $checkoutKey): ?array
    {
        $cacheKey = $this->checkoutPayloadCacheKey($tenantId, $checkoutKey);
        $cachedPayload = Cache::get($cacheKey);

        if (is_array($cachedPayload)) {
            return $cachedPayload;
        }

        $order = Order::query()
            ->where('tenant_id', $tenantId)
            ->where('status', OrderStatus::AwaitingPayment)
            ->where('metadata->checkout_key', $checkoutKey)
            ->with('payment')
            ->latest('id')
            ->first();

        if (! $order?->payment?->provider_token) {
            return null;
        }

        $payload = $this->serializeCheckoutResponse($order, $order->payment->provider_token);
        Cache::put($cacheKey, $payload, now()->addMinutes(15));

        return $payload;
    }

    private function checkoutPayloadCacheKey(int $tenantId, string $checkoutKey): string
    {
        return "checkout:payload:{$tenantId}:{$checkoutKey}";
    }

    private function serializeCheckoutResponse(Order $order, string $iframeToken, ?string $iframeSrc = null): array
    {
        return [
            'merchant_oid' => $order->merchant_oid,
            'order_id' => $order->id,
            'status' => $order->status->value,
            'total_cents' => $order->total_cents,
            'currency' => $order->currency,
            'iframe_token' => $iframeToken,
            'iframe_src' => $iframeSrc ?: rtrim((string) config('paytr.endpoints.iframe_secure'), '/').'/'.$iframeToken,
            'checkout_url' => route('checkout.session', ['order' => $order->checkout_ref]),
        ];
    }
}
