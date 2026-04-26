<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Paytr\PaytrClient;
use App\Support\TenantResolver;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
        ]);

        $tenant = $tenants->resolve($request);
        $user = Auth::guard('api')->user();
        $cartToken = $validated['cart_token'] ?? $request->header('X-Cart-Token');
        $checkoutItems = $this->checkoutItems($validated, $tenant->id, $user, $cartToken);

        abort_if($checkoutItems->isEmpty(), 422, 'Sepet bos.');

        /** @var Order $order */
        $order = DB::transaction(function () use ($validated, $tenant, $user, $cartToken, $checkoutItems): Order {
            $products = Product::query()
                ->whereBelongsTo($tenant)
                ->where('is_active', true)
                ->whereIn('id', $checkoutItems->pluck('product_id')->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $merchantOid = $this->makeMerchantOid();

            $order = Order::query()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $user?->id,
                'merchant_oid' => $merchantOid,
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
                    'stock_reserved' => true,
                    'stock_released' => false,
                ],
            ]);

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

            $order->update([
                'subtotal_cents' => $subtotal,
                'total_cents' => $subtotal,
            ]);

            $order->payment()->create([
                'provider' => 'paytr',
                'merchant_oid' => $merchantOid,
                'status' => PaymentStatus::Pending,
                'amount_cents' => $subtotal,
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
                'message' => 'PayTR odeme oturumu baslatilamadi.',
                'reason' => $exception->getMessage(),
            ], 502);
        }

        $order->payment->update(['provider_token' => $iframe['token']]);

        return response()->json([
            'data' => [
                'merchant_oid' => $order->merchant_oid,
                'order_id' => $order->id,
                'status' => $order->status->value,
                'total_cents' => $order->total_cents,
                'currency' => $order->currency,
                'iframe_token' => $iframe['token'],
                'iframe_src' => $iframe['iframe_src'],
                'blade_checkout_url' => route('paytr.checkout', $order->merchant_oid),
            ],
        ], 201);
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
}
