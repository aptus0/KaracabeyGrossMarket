<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\CartItem;
use App\Models\User;
use App\Services\Auth\ApiTokenIssuer;
use App\Services\Auth\OAuthProviderManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS   = 5;
    private const DECAY_SECONDS  = 900; // 15 dakika

    public function register(Request $request, ApiTokenIssuer $tokenIssuer): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'phone'       => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'    => [
                'required',
                'string',
                Password::min(8)->mixedCase()->numbers()->max(255),
            ],
            'location'    => ['nullable', 'string', 'max:160'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'cart_token'  => ['nullable', 'string', 'max:64'],
        ]);

        $user = User::query()->create([
            'name'          => $validated['name'],
            'phone'         => $this->normalizePhone($validated['phone']),
            'password'      => $validated['password'],
            'last_ip'       => $request->ip(),
            'last_location' => $validated['location'] ?? null,
            'last_login_at' => now(),
        ]);

        $this->claimGuestCart($request, $user);

        return response()->json([
            'user'       => $this->serializeUser($user),
            'token'      => $tokenIssuer->issue($user, (string) $request->input('device_name', 'default')),
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request, ApiTokenIssuer $tokenIssuer): JsonResponse
    {
        $credentials = $request->validate([
            'phone'       => ['required', 'string', 'max:20'],
            'password'    => ['required', 'string'],
            'location'    => ['nullable', 'string', 'max:160'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'cart_token'  => ['nullable', 'string', 'max:64'],
        ]);

        $phone        = $this->normalizePhone($credentials['phone']);
        $throttleKey  = $this->throttleKey($phone, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = (int) ceil($seconds / 60);

            throw ValidationException::withMessages([
                'phone' => "Çok fazla başarısız giriş denemesi. {$minutes} dakika sonra tekrar deneyin.",
            ])->status(429);
        }

        $user = User::query()->where('phone', $phone)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            $remaining = max(0, self::MAX_ATTEMPTS - RateLimiter::attempts($throttleKey));
            $message   = $remaining > 0
                ? "Giriş bilgileri hatalı. {$remaining} deneme hakkınız kaldı."
                : 'Çok fazla başarısız giriş denemesi. Lütfen bekleyin.';

            throw ValidationException::withMessages([
                'phone' => $message,
            ])->withResponse(response()->json([
                'message'            => $message,
                'remaining_attempts' => $remaining,
                'locked'             => $remaining === 0,
            ], 422));
        }

        RateLimiter::clear($throttleKey);

        // Session meta güncelle
        $user->update([
            'last_ip'       => $request->ip(),
            'last_location' => $credentials['location'] ?? $user->last_location,
            'last_login_at' => now(),
        ]);

        $this->claimGuestCart($request, $user);

        return response()->json([
            'user'       => $this->serializeUser($user),
            'token'      => $tokenIssuer->issue($user, (string) $request->input('device_name', 'default')),
            'token_type' => 'Bearer',
        ]);
    }

    public function providers(OAuthProviderManager $providers): JsonResponse
    {
        return response()->json([
            'data' => $providers->statuses(),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var ApiToken|null $token */
        $token = $request->attributes->get('api_token');

        if ($token) {
            $token->delete();
        } elseif ($request->bearerToken()) {
            ApiToken::query()
                ->where('token_hash', hash('sha256', $request->bearerToken()))
                ->delete();
        }

        return response()->json(['data' => ['status' => 'ok']]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Türkiye formatı: başındaki +90 / 0 / boşluk / - temizle → 10 hane */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        // +905xx → 5xx (13 hane → son 10)
        if (strlen($digits) === 12 && str_starts_with($digits, '90')) {
            $digits = substr($digits, 2);
        }

        // 05xx → 5xx
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    private function throttleKey(string $phone, string $ip): string
    {
        return 'login|' . $phone . '|' . $ip;
    }

    private function serializeUser(User $user): array
    {
        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
        ];
    }

    private function claimGuestCart(Request $request, User $user): void
    {
        $cartToken = $request->header('X-Cart-Token') ?: $request->input('cart_token');
        $cartToken = $cartToken ? Str::limit((string) $cartToken, 64, '') : null;

        if (! $cartToken) {
            return;
        }

        $guestItems = CartItem::query()
            ->where('cart_token', $cartToken)
            ->get()
            ->groupBy(fn (CartItem $item): string => $item->tenant_id . '-' . $item->product_id);

        foreach ($guestItems as $items) {
            /** @var CartItem $firstItem */
            $firstItem = $items->first();

            $existing = CartItem::query()
                ->where('tenant_id', $firstItem->tenant_id)
                ->where('user_id', $user->id)
                ->where('product_id', $firstItem->product_id)
                ->first();

            $quantity = min(99, $items->sum('quantity') + ($existing?->quantity ?? 0));

            if ($existing) {
                $existing->update(['quantity' => $quantity]);
                $items->each->delete();
                continue;
            }

            $firstItem->update([
                'user_id'    => $user->id,
                'cart_token' => null,
                'quantity'   => $quantity,
            ]);

            $items->slice(1)->each->delete();
        }
    }
}
