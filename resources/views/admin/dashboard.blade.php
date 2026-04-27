<x-layouts.admin header="Genel Bakış">
    <div class="flex flex-col gap-6">
        <template id="dashboard-chart-data">{{ json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}</template>
        
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="font-semibold tracking-tight text-lg">Toplam Kazanç</h3>
                    <x-lucide-dollar-sign class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="p-6 pt-0">
                    <div class="text-3xl font-bold">{{ number_format($totalRevenue / 100, 2, ',', '.') }} ₺</div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Tüm zamanların toplam geliri
                    </p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="font-semibold tracking-tight text-lg">Siparişler</h3>
                    <x-lucide-shopping-bag class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="p-6 pt-0">
                    <div class="text-3xl font-bold">{{ number_format($totalOrders) }}</div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Tüm zamanların başarılı siparişleri
                    </p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="font-semibold tracking-tight text-lg">Kayıtlı Müşteriler</h3>
                    <x-lucide-users class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="p-6 pt-0">
                    <div class="text-3xl font-bold">{{ number_format($totalCustomers) }}</div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Sistemdeki toplam kullanıcı sayısı
                    </p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="font-semibold tracking-tight text-lg">Toplam Ürün</h3>
                    <x-lucide-package class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="p-6 pt-0">
                    <div class="text-3xl font-bold">{{ number_format($stats['products'] ?? 0) }}</div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Sistemdeki kayıtlı ürünler
                    </p>
                </div>
            </x-ui.card>
        </div>

        <!-- Charts -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
            <x-ui.card class="lg:col-span-4">
                <div class="p-6 pb-0 flex flex-col space-y-1.5">
                    <h3 class="font-semibold tracking-tight">Kazançlar (Son 7 Gün)</h3>
                    <p class="text-sm text-muted-foreground">Günlük satış trendleri</p>
                </div>
                <div class="p-6 pt-4 h-[350px]">
                    <canvas id="earningsChart"></canvas>
                </div>
            </x-ui.card>

            <x-ui.card class="lg:col-span-3">
                <div class="p-6 pb-0 flex flex-col space-y-1.5">
                    <h3 class="font-semibold tracking-tight">Sipariş Hacmi</h3>
                    <p class="text-sm text-muted-foreground">Son 7 günlük sipariş sayısı</p>
                </div>
                <div class="p-6 pt-4 h-[350px]">
                    <canvas id="ordersChart"></canvas>
                </div>
            </x-ui.card>
        </div>

        <!-- Recent Orders Table -->
        <x-ui.card>
            <div class="p-6 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold tracking-tight">Son Siparişler</h3>
                    <p class="text-sm text-muted-foreground">En son gelen siparişler listesi.</p>
                </div>
                <x-ui.button as="a" href="{{ route('admin.orders.index') }}" variant="outline">Tümünü Görüntüle</x-ui.button>
            </div>
            
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Sipariş ID</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Müşteri</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tutar</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tarih</th>
                    </tr>
                </x-slot>
                
                <tr class="border-b transition-colors hover:bg-muted/30">
                    <td class="p-4 px-6 align-middle font-medium">#ORD-001</td>
                    <td class="p-4 px-6 align-middle">Ahmet Yılmaz</td>
                    <td class="p-4 px-6 align-middle">245,50 ₺</td>
                    <td class="p-4 px-6 align-middle">
                        <x-ui.badge variant="default" class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-emerald-500/20">Tamamlandı</x-ui.badge>
                    </td>
                    <td class="p-4 px-6 align-middle text-right text-muted-foreground">2 saat önce</td>
                </tr>
                <tr class="border-b transition-colors hover:bg-muted/30">
                    <td class="p-4 px-6 align-middle font-medium">#ORD-002</td>
                    <td class="p-4 px-6 align-middle">Ayşe Demir</td>
                    <td class="p-4 px-6 align-middle">1.250,00 ₺</td>
                    <td class="p-4 px-6 align-middle">
                        <x-ui.badge variant="secondary" class="bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 border-amber-500/20">İşleniyor</x-ui.badge>
                    </td>
                    <td class="p-4 px-6 align-middle text-right text-muted-foreground">5 saat önce</td>
                </tr>
                <tr class="transition-colors hover:bg-muted/30">
                    <td class="p-4 px-6 align-middle font-medium">#ORD-003</td>
                    <td class="p-4 px-6 align-middle">Mehmet Kaya</td>
                    <td class="p-4 px-6 align-middle">85,90 ₺</td>
                    <td class="p-4 px-6 align-middle">
                        <x-ui.badge variant="destructive" class="bg-red-500/10 text-red-600 hover:bg-red-500/20 border-red-500/20">İptal Edildi</x-ui.badge>
                    </td>
                    <td class="p-4 px-6 align-middle text-right text-muted-foreground">1 gün önce</td>
                </tr>
            </x-ui.table>
        </x-ui.card>
    </div>
</x-layouts.admin>
