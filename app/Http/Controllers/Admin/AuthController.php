<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; // DOSYA YAZMA İŞLEMİ İÇİN EKLENDİ
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // 1. Gelen Verileri Doğrula
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. LOGLAMA İŞLEMİ 
        // (Şifre doğrulamadan önce yapıyoruz ki, hatalı denemeler de kayıt altına alınsın)
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');
        $emailAttempt = $request->input('email');
        $date = now()->format('Y-m-d H:i:s');

        $logText = "[{$date}] DENEME | E-posta: {$emailAttempt} | IP: {$ipAddress} | Cihaz: {$userAgent}" . PHP_EOL;
        
        // storage/logs/admin_login_logs.txt dosyasına ekle
        File::append(storage_path('logs/admin_login_logs.txt'), $logText);

        // 3. Şifre ve Yetki Kontrolü
        if (! Auth::attempt($credentials + ['is_admin' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Admin giriş bilgileri hatalı veya yetkisiz erişim.',
            ]);
        }

        // 4. Başarılı Giriş - Oturumu Yenile ve Yönlendir
        $request->session()->regenerate();

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