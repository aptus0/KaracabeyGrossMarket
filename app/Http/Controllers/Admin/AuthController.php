<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Security\AdminAccessInspector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request, AdminAccessInspector $inspector): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attempted = Auth::attempt($credentials + ['is_admin' => true], $request->boolean('remember'));
        $inspector->recordLoginAttempt($request, (string) $credentials['email'], $attempted);

        if (! $attempted) {
            throw ValidationException::withMessages([
                'email' => 'Admin giriş bilgileri hatalı veya yetkisiz erişim.',
            ]);
        }

        $request->session()->regenerate();
        $request->user()?->forceFill([
            'last_ip' => $request->ip(),
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
