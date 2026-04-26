<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = User::query()->create($validated);

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $this->issueToken($user, $request),
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Giris bilgileri hatali.',
            ]);
        }

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $this->issueToken($user, $request),
                'token_type' => 'Bearer',
            ],
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

    private function issueToken(User $user, Request $request): string
    {
        $plainToken = Str::random(64);

        $user->apiTokens()->create([
            'name' => (string) $request->input('device_name', 'default'),
            'token_hash' => hash('sha256', $plainToken),
            'abilities' => ['*'],
        ]);

        return $plainToken;
    }
}
