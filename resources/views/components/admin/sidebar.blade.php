<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-50 w-72 shrink-0 -translate-x-full overflow-y-auto border-r border-orange-100/80 bg-white/95 shadow-[0_24px_80px_rgba(15,23,42,0.14)] backdrop-blur transition-transform md:static md:z-auto md:translate-x-0 md:shadow-[0_12px_36px_rgba(249,115,22,0.08)]"
>
    <div class="sticky top-0 z-10 border-b border-orange-100/80 bg-white/90 px-6 py-5 backdrop-blur">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-orange-500 via-orange-400 to-amber-300 shadow-[0_18px_36px_rgba(249,115,22,0.32)]">
                <img src="{{ asset('assets/kgm-logo-4k.png') }}" alt="Karacabey Gross" class="h-9 w-auto drop-shadow-sm">
            </div>
            <div class="min-w-0">
                <p class="truncate text-[11px] font-semibold uppercase tracking-[0.32em] text-orange-500/80">Admin Console</p>
                <p class="truncate text-base font-semibold text-slate-900">Karacabey Gross</p>
                <p class="truncate text-xs text-slate-500">Turuncu-beyaz operasyon merkezi</p>
            </div>
        </a>
    </div>

    <div class="space-y-6 p-4 pb-20">
        <div class="rounded-3xl border border-orange-100 bg-gradient-to-br from-orange-500 via-orange-400 to-amber-300 p-4 text-white shadow-[0_20px_45px_rgba(249,115,22,0.22)]">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-white/80">Canli Yonetim</p>
            <p class="mt-2 text-lg font-semibold">Siparis, finans ve katalog ayni akista</p>
            <p class="mt-1 text-sm text-white/80">Gunluk operasyonu tek panelden daha net takip edin.</p>
        </div>

        <!-- Genel Bakış -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Genel Bakis</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-layout-dashboard class="h-4 w-4" />
                    Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-shopping-cart class="h-4 w-4" />
                    Siparişler
                </a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-credit-card class="h-4 w-4" />
                    Ödemeler (PayTR)
                </a>
            </nav>
        </div>

        <!-- Katalog -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Katalog</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-package class="h-4 w-4" />
                    Ürünler
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-tags class="h-4 w-4" />
                    Kategoriler
                </a>
            </nav>
        </div>

        <!-- Pazarlama ve Müşteriler -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Pazarlama ve Kullanicilar</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-users class="h-4 w-4" />
                    Müşteriler & Yetkililer
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.campaigns.*', 'admin.coupons.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-ticket class="h-4 w-4" />
                    Kampanya & Kuponlar
                </a>
            </nav>
        </div>

        <!-- İçerik Yönetimi (CMS) -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Icerik Yonetimi</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.pages.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-file-text class="h-4 w-4" />
                    Sayfalar
                </a>
                <a href="{{ route('admin.homepage-blocks.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.homepage-blocks.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-layout-template class="h-4 w-4" />
                    Ana Sayfa Vitrini
                </a>
                <a href="{{ route('admin.navigation.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.navigation.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-menu-square class="h-4 w-4" />
                    Menü Navigasyonu
                </a>
            </nav>
        </div>

        <!-- Ayarlar -->
        <div>
            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Sistem</h4>
            <nav class="space-y-1">
                <a href="{{ route('admin.marketing.edit') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all {{ request()->routeIs('admin.marketing.*') ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white shadow-[0_14px_32px_rgba(249,115,22,0.24)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                    <x-lucide-bar-chart-2 class="h-4 w-4" />
                    Google & Meta (Pixel)
                </a>
                
                <form action="{{ route('admin.logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium text-rose-600 transition-all hover:bg-rose-50">
                        <x-lucide-log-out class="h-4 w-4" />
                        Çıkış Yap
                    </button>
                </form>
            </nav>
        </div>
    </div>
</aside>
<!-- Mobile Overlay -->
<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-950/30 backdrop-blur-sm md:hidden"></div>
