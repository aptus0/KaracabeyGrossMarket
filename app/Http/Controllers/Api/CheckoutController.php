<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\Paytr\PaytrClient;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $tenant = $tenants->resolve($request);

        /** @var Order $order */
        $order = DB::transaction(function () use ($validated, $tenant, $request): Order {
            $productIds = collect($validated['items'])->pluck('product_id')->all();
            $products = Product::query()
                ->whereBelongsTo($tenant)
                ->where('is_active', true)
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $merchantOid = $this->makeMerchantOid();

            $order = Order::query()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $request->user()?->id,
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
                'metadata' => ['source' => 'api_checkout'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);

                abort_if(! $product, 422, 'Sepette gecersiz urun var.');
                abort_if($product->stock_quantity < $item['quantity'], 422, $product->name.' icin stok yetersiz.');

                $lineTotal = $product->price_cents * $item['quantity'];
                $subtotal += $lineTotal;

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

    private function makeMerchantOid(): string
    {
        do {
            $oid = 'KGM'.now()->format('ymdHis').Str::upper(Str::random(10));
        } while (Order::query()->where('merchant_oid', $oid)->exists());

        return $oid;
    }
}
