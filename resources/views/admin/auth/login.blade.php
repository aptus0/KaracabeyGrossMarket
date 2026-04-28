<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="csp-nonce" nonce="{{ request()->attributes->get('csp_nonce') }}">
    <title>Yönetim Paneli Girişi — Karacabey Gross Market</title>
    @vite(['resources/css/app.css'])
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        /* Login sayfası CSS güvenlik ağı */
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; }
        .login-wrap {
            min-height: 100svh;
            display: flex;
            background: var(--background);
        }
        .login-sidebar {
            display: none;
            flex-direction: column;
            width: 42%;
            min-height: 100svh;
            position: sticky;
            top: 0;
            padding: 2.5rem;
            background: var(--primary);
            color: white;
        }
        @media (min-width: 1024px) { .login-sidebar { display: flex; } }
        .login-sidebar svg { width: 24px; height: 24px; }
        .login-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-form-box {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--primary);
        }
        .login-logo svg { width: 2.5rem; height: 2.5rem; }
        .login-header { text-align: center; }
        .login-header h1 { font-size: 1.5rem; font-weight: 700; margin: 0.5rem 0 0.25rem; }
        .login-header p { font-size: 0.875rem; color: var(--muted-foreground); margin: 0; }
    </style>
</head>
<body>
<div class="login-wrap">

    {{-- Sol Panel --}}
    <aside class="login-sidebar">
        <div class="flex items-center gap-2 text-lg font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/>
            </svg>
            Karacabey Gross Market
        </div>

        <div class="mt-auto">
            <blockquote>
                <p class="text-lg leading-relaxed opacity-90">
                    &ldquo;Tüm mağaza operasyonlarını, sipariş süreçlerini ve pazarlama verilerini tek noktadan kolayca yönetin.&rdquo;
                </p>
                <footer class="mt-4 text-sm opacity-70">— Sistem Yönetimi</footer>
            </blockquote>
        </div>
    </aside>

    {{-- Sağ — Form --}}
    <main class="login-main">
        <div class="login-form-box">

            {{-- Logo & Başlık --}}
            <div class="login-header">
                <div class="login-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1>Yönetim Paneli</h1>
                <p>E-posta ve şifrenizle giriş yapın</p>
            </div>

            <x-ui.card class="w-full">
                <div class="p-6">

                    {{-- Başarı mesajı --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Validasyon hataları --}}
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-destructive/20 bg-destructive/10 px-4 py-3">
                            <div class="flex items-center gap-2 text-sm font-semibold text-destructive">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                Giriş başarısız
                            </div>
                            <ul class="mt-2 space-y-1 text-xs text-destructive/80 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.login.store') }}" class="grid gap-4">
                        @csrf

                        <div class="grid gap-1.5">
                            <x-ui.label for="email">E-posta Adresi</x-ui.label>
                            <x-ui.input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="isim@sirket.com"
                                required
                                autofocus
                                autocomplete="email"
                                value="{{ old('email') }}"
                            />
                        </div>

                        <div class="grid gap-1.5">
                            <x-ui.label for="password">Şifre</x-ui.label>
                            <x-ui.input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <x-ui.checkbox id="remember" name="remember" />
                            <x-ui.label for="remember" class="cursor-pointer text-sm">Beni Hatırla</x-ui.label>
                        </div>

                        <x-ui.button type="submit" class="w-full font-semibold">
                            Giriş Yap
                        </x-ui.button>
                    </form>
                </div>
            </x-ui.card>

            <p class="text-center text-sm text-muted-foreground">
                Sorun mu yaşıyorsunuz?
                <a href="#" class="underline underline-offset-4 hover:text-foreground">Sistem yöneticisi ile iletişime geçin.</a>
            </p>
        </div>
    </main>
</div>
</body>
</html>
