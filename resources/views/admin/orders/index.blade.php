<x-layouts.admin header="Siparişler">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Siparişler</h2>
                <p class="text-muted-foreground">Müşteri siparişlerini yönetin ve takip edin.</p>
            </div>
            <x-ui.button as="a" href="#" variant="outline">
                <x-lucide-download class="mr-2 h-4 w-4" /> CSV İndir
            </x-ui.button>
        </div>

        <x-ui.card>
            <div class="p-6 pb-0 border-b pb-6 flex items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                    <x-ui.input type="search" name="q" placeholder="Sipariş ID veya müşteri adı ile arayın..." class="pl-9 bg-muted/50" />
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.select class="w-[180px]">
                        <option value="">Tüm Durumlar</option>
                        <option value="pending">Bekliyor</option>
                        <option value="processing">İşleniyor</option>
                        <option value="completed">Tamamlandı</option>
                        <option value="cancelled">İptal Edildi</option>
                    </x-ui.select>
                </div>
            </div>
            
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Sipariş ID</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Müşteri</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tutar</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tarih</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İşlemler</th>
                    </tr>
                </x-slot>

                @forelse($orders as $order)
                    <tr class="border-b transition-colors hover:bg-muted/30 group">
                        <td class="p-4 px-6 align-middle font-mono text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline hover:text-primary transition-colors">
                                {{ $order->merchant_oid }}
                            </a>
                        </td>
                        <td class="p-4 px-6 align-middle">
                            <div class="font-medium text-sm">{{ $order->customer_name }}</div>
                            <div class="text-xs text-muted-foreground">{{ $order->customer_email }}</div>
                        </td>
                        <td class="p-4 px-6 align-middle font-semibold text-sm">
                            {{ number_format($order->total_cents / 100, 2, ',', '.') }} {{ $order->currency }}
                        </td>
                        <td class="p-4 px-6 align-middle">
                            <x-ui.badge variant="secondary">{{ $order->status->value }}</x-ui.badge>
                        </td>
                        <td class="p-4 px-6 align-middle text-sm text-muted-foreground">
                            {{ $order->created_at?->format('d.m.Y H:i') }}
                        </td>
                        <td class="p-4 px-6 align-middle text-right">
                            <x-ui.button variant="ghost" size="icon" as="a" href="{{ route('admin.orders.show', $order) }}">
                                <x-lucide-eye class="h-4 w-4" />
                            </x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <x-lucide-inbox class="h-10 w-10 text-muted-foreground/50" />
                                <p class="text-lg font-medium">Sipariş bulunamadı</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if($orders->hasPages())
                <div class="p-4 px-6 border-t bg-muted/20">
                    {{ $orders->links('pagination::tailwind') }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
