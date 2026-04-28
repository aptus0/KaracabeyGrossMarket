<x-layouts.admin header="Yonetim Ozeti">
    @php
        $formatMoney = static fn (int $cents): string => number_format($cents / 100, 2, ',', '.').' ₺';
    @endphp

    <div class="flex flex-col gap-6">
        <template id="dashboard-chart-data">{{ json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}</template>

        <section class="relative overflow-hidden rounded-[2rem] border border-orange-200/70 bg-white px-6 py-6 shadow-[0_30px_80px_rgba(249,115,22,0.12)]">
            <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-gradient-to-l from-orange-100/80 via-orange-50/60 to-transparent lg:block"></div>
            <div class="relative grid gap-6 lg:grid-cols-[1.4fr_0.9fr] lg:items-center">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-orange-600">
                        Enterprise Dashboard
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-4xl">{{ $tenant->name }}</h2>
                        <p class="max-w-2xl text-sm leading-6 text-slate-600 md:text-base">
                            Finansal tahsilat, satılan ürün adedi ve katalog operasyonunu tek ekrandan izleyin.
                            Gelir metrikleri ile ürün hareketi ayrı hesaplanır; bu sayede yönetim kararları daha net okunur.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-4 py-3 text-sm font-semibold text-white shadow-[0_16px_32px_rgba(249,115,22,0.24)] transition hover:opacity-95">
                            Siparisleri Ac
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-orange-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-600">
                            Katalogu Yonet
                        </a>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl border border-orange-100 bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Net Tahsilat</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['net_revenue_cents']) }}</p>
                        <p class="mt-2 text-sm text-slate-500">Iadeler dusulmus toplam gelir</p>
                    </div>
                    <div class="rounded-3xl border border-orange-100 bg-slate-950 p-5 text-white shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-orange-200/80">Satilan Urun</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight">{{ number_format($metrics['sold_units']) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Basarili siparislerden hesaplanan adet</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-ui.card class="overflow-hidden rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between p-6 pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Net Tahsilat</p>
                        <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['net_revenue_cents']) }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <x-lucide-wallet class="h-5 w-5" />
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <p class="text-sm text-slate-500">Toplanan odeme eksi iade tutari</p>
                    <p class="mt-3 text-sm font-medium {{ $trends['net_revenue']['classes'] }}">{{ $trends['net_revenue']['label'] }}</p>
                </div>
            </x-ui.card>

            <x-ui.card class="overflow-hidden rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between p-6 pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Satilan Urun</p>
                        <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ number_format($metrics['sold_units']) }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <x-lucide-package-check class="h-5 w-5" />
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <p class="text-sm text-slate-500">Sadece basarili siparis kalemleri</p>
                    <p class="mt-3 text-sm font-medium {{ $trends['sold_units']['classes'] }}">{{ $trends['sold_units']['label'] }}</p>
                </div>
            </x-ui.card>

            <x-ui.card class="overflow-hidden rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between p-6 pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Basarili Siparis</p>
                        <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ number_format($metrics['successful_orders']) }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <x-lucide-shopping-bag class="h-5 w-5" />
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <p class="text-sm text-slate-500">Odendi, hazirlaniyor, kargoda ve teslim edildi</p>
                    <p class="mt-3 text-sm font-medium {{ $trends['orders']['classes'] }}">{{ $trends['orders']['label'] }}</p>
                </div>
            </x-ui.card>

            <x-ui.card class="overflow-hidden rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between p-6 pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Ortalama Sepet</p>
                        <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['average_basket_cents']) }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <x-lucide-receipt class="h-5 w-5" />
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <p class="text-sm text-slate-500">Siparis basina net tahsilat ortalamasi</p>
                    <p class="mt-3 text-sm font-medium {{ $trends['average_basket']['classes'] }}">{{ $trends['average_basket']['label'] }}</p>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.35fr_0.95fr]">
            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="grid gap-4 p-6 md:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Brut Siparis Hacmi</p>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['gross_sales_cents']) }}</p>
                        <p class="mt-2 text-sm text-slate-500">Indirim oncesi siparis ve sevkiyat toplami</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Toplam Iskonto</p>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['discount_cents']) }}</p>
                        <p class="mt-2 text-sm text-slate-500">Kupon ve kampanya kaynakli indirimler</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Iade Tutari</p>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">{{ $formatMoney($metrics['refund_cents']) }}</p>
                        <p class="mt-2 text-sm text-slate-500">Basarili odemelere ait kayitli iadeler</p>
                    </div>
                    <div class="rounded-3xl border border-orange-200 bg-gradient-to-br from-orange-500 to-amber-400 p-5 text-white shadow-[0_18px_36px_rgba(249,115,22,0.2)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-orange-100">Bekleyen Tahsilat</p>
                        <p class="mt-3 text-2xl font-semibold tracking-tight">{{ $formatMoney($metrics['awaiting_payment_cents']) }}</p>
                        <p class="mt-2 text-sm text-orange-50/85">{{ number_format($metrics['awaiting_payment_count']) }} siparis odeme bekliyor</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-slate-950">Katalog ve Musteri Ozetleri</h3>
                            <p class="text-sm text-slate-500">Operasyon icin hizli referans kartlari</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                            <x-lucide-building-2 class="h-5 w-5" />
                        </div>
                    </div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Toplam Urun</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ number_format($metrics['catalog_products']) }}</p>
                            <p class="mt-1 text-sm text-slate-500">Katalogdaki tum SKU kayitlari</p>
                        </div>
                        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Aktif SKU</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ number_format($metrics['active_products']) }}</p>
                            <p class="mt-1 text-sm text-slate-500">Satisa acik urun sayisi</p>
                        </div>
                        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Musteri Havuzu</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ number_format($metrics['customer_count']) }}</p>
                            <p class="mt-1 text-sm text-slate-500">Siparis vermis benzersiz e-posta</p>
                        </div>
                        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Basarili Odeme</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ number_format($metrics['paid_payments']) }}</p>
                            <p class="mt-1 text-sm text-slate-500">Onayli odeme kaydi adedi</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)] lg:col-span-4">
                <div class="flex flex-col space-y-1.5 p-6 pb-0">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-950">Gunluk Tahsilat</h3>
                    <p class="text-sm text-slate-500">Son 7 gunde tahsil edilen gelir akisi</p>
                </div>
                <div class="h-[340px] p-6 pt-4">
                    <canvas id="earningsChart"></canvas>
                </div>
            </x-ui.card>

            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)] lg:col-span-3">
                <div class="flex flex-col space-y-1.5 p-6 pb-0">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-950">Gunluk Satilan Urun</h3>
                    <p class="text-sm text-slate-500">Siparis kalemlerinden hesaplanan adetler</p>
                </div>
                <div class="h-[340px] p-6 pt-4">
                    <canvas id="ordersChart"></canvas>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.3fr_0.9fr]">
            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="flex items-center justify-between p-6">
                    <div>
                        <h3 class="text-lg font-semibold tracking-tight text-slate-950">Son Siparisler</h3>
                        <p class="text-sm text-slate-500">Gercek siparis hareketleri ve durum dagilimi.</p>
                    </div>
                    <x-ui.button as="a" href="{{ route('admin.orders.index') }}" variant="outline" class="rounded-2xl border-orange-200 bg-white text-slate-700 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-600">Tumunu Goruntule</x-ui.button>
                </div>

                <x-ui.table>
                    <x-slot name="header">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Siparis ID</th>
                            <th class="h-12 px-6 text-left align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Musteri</th>
                            <th class="h-12 px-6 text-left align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Urun</th>
                            <th class="h-12 px-6 text-left align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Tutar</th>
                            <th class="h-12 px-6 text-left align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Durum</th>
                            <th class="h-12 px-6 text-right align-middle text-xs font-medium uppercase tracking-wider text-slate-400">Tarih</th>
                        </tr>
                    </x-slot>

                    @forelse($orders as $order)
                        <tr class="border-b border-slate-100 transition-colors hover:bg-orange-50/40">
                            <td class="p-4 px-6 align-middle">
                                <a href="{{ $order['show_url'] }}" class="font-mono text-sm font-semibold text-slate-900 transition hover:text-orange-600">
                                    {{ $order['merchant_oid'] }}
                                </a>
                            </td>
                            <td class="p-4 px-6 align-middle">
                                <div class="font-medium text-slate-900">{{ $order['customer_name'] }}</div>
                                <div class="text-xs text-slate-500">{{ $order['customer_email'] }}</div>
                            </td>
                            <td class="p-4 px-6 align-middle text-sm text-slate-600">
                                {{ number_format($order['units_count']) }} kalem
                            </td>
                            <td class="p-4 px-6 align-middle text-sm font-semibold text-slate-900">
                                {{ number_format($order['total_cents'] / 100, 2, ',', '.') }} {{ $order['currency'] }}
                            </td>
                            <td class="p-4 px-6 align-middle">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $order['status_classes'] }}">
                                    {{ $order['status_label'] }}
                                </span>
                            </td>
                            <td class="p-4 px-6 text-right align-middle">
                                <div class="text-sm text-slate-700">{{ $order['created_at_human'] }}</div>
                                <div class="text-xs text-slate-500">{{ $order['created_at_formatted'] }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Henuz siparis kaydi bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </x-ui.table>
            </x-ui.card>

            <x-ui.card class="rounded-[1.75rem] border-orange-100 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-slate-950">En Cok Satan Urunler</h3>
                            <p class="text-sm text-slate-500">Basarili siparislerden olusan ilk 5 urun</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                            <x-lucide-badge-percent class="h-5 w-5" />
                        </div>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($topProducts as $product)
                            <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50/70 px-4 py-4">
                                <div class="min-w-0 pr-4">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ number_format((int) $product->units_sold) }} adet satildi</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-slate-900">{{ $formatMoney((int) $product->revenue_cents) }}</p>
                                    <p class="mt-1 text-xs text-slate-500">brut satis hacmi</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-8 text-center text-sm text-slate-500">
                                Henuz urun satis verisi olusmadi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </section>
    </div>
</x-layouts.admin>
