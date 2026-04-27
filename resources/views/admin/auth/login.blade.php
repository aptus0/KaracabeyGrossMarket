<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli Girişi - Karacabey Gross</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-background antialiased flex items-center justify-center p-4 selection:bg-primary selection:text-primary-foreground">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex lg:w-1/2 lg:fixed lg:inset-y-0 lg:left-0">
        <div class="absolute inset-0 bg-primary/90"></div>
        <div class="relative z-20 flex items-center text-lg font-medium">
            <x-lucide-store class="mr-2 h-6 w-6" />
            Karacabey Gross A.Ş.
        </div>
        <div class="relative z-20 mt-auto">
            <blockquote class="space-y-2">
                <p class="text-lg">
                    &ldquo;Tüm mağaza operasyonlarını, sipariş süreçlerini ve pazarlama verilerini tek noktadan kolayca yönetin.&rdquo;
                </p>
                <footer class="text-sm text-primary-foreground/80">Sistem Yönetimi</footer>
            </blockquote>
        </div>
    </div>

    <div class="w-full max-w-sm lg:p-8 lg:absolute lg:right-[10%] lg:top-1/2 lg:-translate-y-1/2 flex flex-col items-center">
        <div class="flex flex-col space-y-2 text-center mb-8">
            <x-lucide-shield-check class="mx-auto h-12 w-12 text-primary" />
            <h1 class="text-2xl font-semibold tracking-tight">Yönetim Paneline Giriş</h1>
            <p class="text-sm text-muted-foreground">Sisteme erişmek için e-posta ve şifrenizi girin</p>
        </div>

        <x-ui.card class="w-full border-none shadow-none bg-transparent">
            <div class="p-0">
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 text-sm text-emerald-600 border border-emerald-500/20 bg-emerald-500/10 rounded-md p-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 border border-destructive/20 bg-destructive/10 rounded-md p-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-destructive mb-1">
                                <x-lucide-alert-circle class="h-4 w-4" /> Giriş Başarısız
                            </div>
                            <ul class="text-xs text-destructive/80 space-y-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-6">
                        <div class="grid gap-4">
                            <div class="grid gap-2 relative">
                                <x-ui.label for="email" class="sr-only">E-posta Adresi</x-ui.label>
                                <x-lucide-mail class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                                <x-ui.input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    placeholder="isim@sirket.com" 
                                    class="pl-9 bg-background/50 focus:bg-background h-10"
                                    required 
                                    autofocus 
                                    autocomplete="email" 
                                    value="{{ old('email') }}"
                                />
                            </div>
                            <div class="grid gap-2 relative">
                                <x-ui.label for="password" class="sr-only">Şifre</x-ui.label>
                                <x-lucide-lock class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                                <x-ui.input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    class="pl-9 bg-background/50 focus:bg-background h-10"
                                    required 
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <x-ui.checkbox id="remember" name="remember" />
                            <x-ui.label for="remember" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
                                Beni Hatırla
                            </x-ui.label>
                        </div>

                        <x-ui.button type="submit" class="w-full font-semibold">
                            Giriş Yap
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </x-ui.card>

        <p class="px-8 mt-8 text-center text-sm text-muted-foreground">
            Sorun mu yaşıyorsunuz? <br> <a href="#" class="underline underline-offset-4 hover:text-primary">Sistem Yöneticisi</a> ile iletişime geçin.
        </p>
    </div>
</body>
</html>