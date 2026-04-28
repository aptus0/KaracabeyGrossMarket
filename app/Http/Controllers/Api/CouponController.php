<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validate(Request $request, TenantResolver $tenants): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'subtotal_cents' => ['required', 'integer', 'min:1'],
        ]);

        $tenant = $tenants->resolve($request);
        $code = strtoupper(trim((string) $validated['code']));
        $subtotalCents = (int) $validated['subtotal_cents'];

        $coupon = Coupon::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return response()->json(['message' => 'Kupon kodu geçersiz.'], 422);
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json(['message' => 'Bu kupon henüz aktif değil.'], 422);
        }

        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            return response()->json(['message' => 'Bu kuponun süresi dolmuş.'], 422);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['message' => 'Bu kuponun kullanım limiti dolmuş.'], 422);
        }

        if ($subtotalCents < $coupon->minimum_order_cents) {
            $minAmount = number_format($coupon->minimum_order_cents / 100, 2, ',', '.') . ' ₺';

            return response()->json(['message' => "Bu kupon için minimum sipariş tutarı {$minAmount}."], 422);
        }

        $discountCents = $coupon->discount_type === 'percent'
            ? (int) round($subtotalCents * $coupon->discount_value / 100)
            : min($coupon->discount_value, $subtotalCents);

        return response()->json([
            'data' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_cents' => $discountCents,
                'total_cents' => max(0, $subtotalCents - $discountCents),
            ],
        ]);
    }
}
