<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Str;

class ApiTokenIssuer
{
    public function issue(User $user, string $deviceName = 'default'): string
    {
        $plainToken = Str::random(64);

        $user->apiTokens()->create([
            'name' => $deviceName,
            'token_hash' => hash('sha256', $plainToken),
            'abilities' => ['*'],
        ]);

        return $plainToken;
    }
}
