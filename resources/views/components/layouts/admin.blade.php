<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta property="csp-nonce" nonce="{{ request()->attributes->get('csp_nonce') }}">
    <title>{{ config('app.name', 'Karacabey Gross Market Admin') }}</title>
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        :root {
            --background: #fffaf5;
            --foreground: #1f2937;
            --card: #ffffff;
            --card-foreground: #111827;
            --popover: #ffffff;
            --popover-foreground: #111827;
            --primary: #f97316;
            --primary-foreground: #ffffff;
            --secondary: #ffedd5;
            --secondary-foreground: #9a3412;
            --muted: #fff4ea;
            --muted-foreground: #7c6f64;
            --accent: #ffedd5;
            --accent-foreground: #9a3412;
            --destructive: #dc2626;
            --destructive-foreground: #ffffff;
            --border: #fed7aa;
            --input: #fed7aa;
            --ring: #fb923c;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(251, 146, 60, 0.16), transparent 28%),
                linear-gradient(180deg, #fffaf5 0%, #fff7ed 42%, #f8fafc 100%);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    @stack('styles')
    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-background font-sans antialiased text-foreground">
    @php
        $authUser = auth()->user();
        $initials = collect(explode(' ', trim((string) ($authUser?->name ?? 'Admin'))))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div class="flex min-h-screen w-full" data-admin-shell data-sidebar-open="false">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <!-- Main Content -->
        <div class="flex min-w-0 flex-1 flex-col">
            <!-- Header -->
            <header class="sticky top-0 z-20 flex h-20 shrink-0 items-center gap-4 border-b border-orange-100/80 bg-white/85 px-4 shadow-[0_18px_40px_rgba(251,146,60,0.08)] backdrop-blur md:px-6">
                <button type="button" data-sidebar-toggle class="rounded-xl border border-orange-100 bg-white p-2 text-slate-600 shadow-sm transition hover:border-orange-200 hover:text-orange-600 md:hidden">
                    <x-lucide-menu class="h-6 w-6" />
                </button>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-orange-500/75">Karacabey Gross Market</p>
                    <h1 class="truncate text-xl font-semibold tracking-tight text-slate-900 md:text-2xl">{{ $header ?? 'Dashboard' }}</h1>
                </div>
                <div class="hidden items-center gap-3 rounded-2xl border border-orange-100/80 bg-white px-4 py-2 shadow-sm lg:flex">
                    <img src="{{ asset('assets/kgm-logo-4k.png') }}" alt="Karacabey Gross" class="h-9 w-auto">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Kurumsal Operasyon Paneli</p>
                        <p class="text-xs text-slate-500">Finans, siparis ve katalog takibi tek ekranda</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/" target="_blank" rel="noreferrer" class="hidden rounded-xl border border-orange-100 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:border-orange-200 hover:text-orange-600 md:inline-flex">
                        Magazayi Gor
                    </a>
                    <button class="relative rounded-full border border-orange-100 bg-white p-2 text-slate-500 shadow-sm transition hover:border-orange-200 hover:text-orange-600">
                        <x-lucide-bell class="h-5 w-5" />
                        <span class="absolute right-1.5 top-1 h-2 w-2 rounded-full bg-orange-500"></span>
                    </button>
                    <div class="flex items-center gap-3 rounded-2xl border border-orange-100 bg-white px-3 py-2 shadow-sm">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 font-bold text-white shadow-[0_10px_30px_rgba(249,115,22,0.25)]">
                            {{ $initials ?: 'A' }}
                        </div>
                        <div class="hidden text-left sm:block">
                            <p class="text-sm font-semibold text-slate-900">{{ $authUser?->name ?? 'Admin' }}</p>
                            <p class="text-xs text-slate-500">{{ $authUser?->email ?? 'yonetici' }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-4 md:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-ui.toaster />
    @stack('scripts')
</body>
</html>
