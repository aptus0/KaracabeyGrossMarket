<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-50 w-64 shrink-0 -translate-x-full border-r bg-card shadow-xl transition-transform md:static md:z-auto md:translate-x-0 md:shadow-none overflow-y-auto"
>
    <div class="h-16 flex items-center px-6 border-b bg-card sticky top-0 z-10">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight text-primary">
            <x-lucide-store class="h-6 w-6" />
            Karacabey Gross
        </a>
    </div>

    <div class="p-4 space-y-6 pb-20">
        <!-- Genel Bakış -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Genel Bakış</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-layout-dashboard class="h-4 w-4" />
                    Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-shopping-cart class="h-4 w-4" />
                    Siparişler
                </a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-credit-card class="h-4 w-4" />
                    Ödemeler (PayTR)
                </a>
            </nav>
        </div>

        <!-- Katalog -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Katalog</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-package class="h-4 w-4" />
                    Ürünler
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-tags class="h-4 w-4" />
                    Kategoriler
                </a>
            </nav>
        </div>

        <!-- Pazarlama ve Müşteriler -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Pazarlama & Kullanıcılar</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-users class="h-4 w-4" />
                    Müşteriler & Yetkililer
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.campaigns.*', 'admin.coupons.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-ticket class="h-4 w-4" />
                    Kampanya & Kuponlar
                </a>
            </nav>
        </div>

        <!-- İçerik Yönetimi (CMS) -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">İçerik Yönetimi</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-file-text class="h-4 w-4" />
                    Sayfalar
                </a>
                <a href="{{ route('admin.homepage-blocks.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.homepage-blocks.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-layout-template class="h-4 w-4" />
                    Ana Sayfa Vitrini
                </a>
                <a href="{{ route('admin.navigation.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.navigation.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-menu-square class="h-4 w-4" />
                    Menü Navigasyonu
                </a>
            </nav>
        </div>

        <!-- Ayarlar -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Sistem</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.marketing.edit') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.marketing.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-bar-chart-2 class="h-4 w-4" />
                    Google & Meta (Pixel)
                </a>
                
                <form action="{{ route('admin.logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 transition-colors">
                        <x-lucide-log-out class="h-4 w-4" />
                        Çıkış Yap
                    </button>
                </form>
            </nav>
        </div>
    </div>
</aside>
<!-- Mobile Overlay -->
<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-background/80 backdrop-blur-sm md:hidden"></div>
