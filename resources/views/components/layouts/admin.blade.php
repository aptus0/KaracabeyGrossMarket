<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Karacabey Gross Market Admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-background font-sans antialiased text-foreground">
    <div class="flex min-h-screen w-full" data-admin-shell data-sidebar-open="false">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="sticky top-0 z-10 flex h-16 shrink-0 items-center gap-4 border-b bg-background px-6 shadow-sm">
                <button type="button" data-sidebar-toggle class="md:hidden">
                    <x-lucide-menu class="h-6 w-6" />
                </button>
                <div class="flex-1">
                    <h1 class="text-xl font-semibold tracking-tight">{{ $header ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative rounded-full p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                        <x-lucide-bell class="h-5 w-5" />
                        <span class="absolute top-1 right-1.5 h-2 w-2 rounded-full bg-destructive"></span>
                    </button>
                    <div class="h-9 w-9 rounded-full bg-primary flex items-center justify-center font-bold text-primary-foreground shadow-sm">
                        A
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-ui.toaster />
</body>
</html>
