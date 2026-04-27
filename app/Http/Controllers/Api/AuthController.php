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
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, ApiTokenIssuer $tokenIssuer): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'cart_token' => ['nullable', 'string', 'max:64'],
        ]);

        $user = User::query()->create($validated);
        $this->claimGuestCart($request, $user);

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $tokenIssuer->issue($user, (string) $request->input('device_name', 'default')),
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(Request $request, ApiTokenIssuer $tokenIssuer): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'cart_token' => ['nullable', 'string', 'max:64'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Giris bilgileri hatali.',
            ]);
        }

        $this->claimGuestCart($request, $user);

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $tokenIssuer->issue($user, (string) $request->input('device_name', 'default')),
                'token_type' => 'Bearer',
            ],
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
            ->groupBy(fn (CartItem $item): string => $item->tenant_id.'-'.$item->product_id);

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
                'user_id' => $user->id,
                'cart_token' => null,
                'quantity' => $quantity,
            ]);

            $items->slice(1)->each->delete();
        }
    }
}
