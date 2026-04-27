<x-layouts.admin :header="'Sipariş ' . $order->merchant_oid">
    <div class="flex flex-col gap-6 max-w-5xl mx-auto w-full">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Sipariş {{ $order->merchant_oid }}</h2>
                <p class="text-muted-foreground">{{ $order->created_at?->format('d.m.Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <x-ui.badge variant="secondary" class="text-sm px-3 py-1">{{ $order->status->value }}</x-ui.badge>
                <x-ui.button as="a" href="{{ route('admin.orders.index') }}" variant="outline">
                    <x-lucide-arrow-left class="mr-2 h-4 w-4" /> Geri Dön
                </x-ui.button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card>
                <div class="p-6 border-b">
                    <h3 class="font-semibold tracking-tight">Müşteri Bilgileri</h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-2">
                        <span class="text-muted-foreground">İsim:</span>
                        <span class="font-medium">{{ $order->customer_name }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-muted-foreground">E-posta:</span>
                        <span class="font-medium">{{ $order->customer_email }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-muted-foreground">Telefon:</span>
                        <span class="font-medium">{{ $order->customer_phone }}</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="p-6 border-b">
                    <h3 class="font-semibold tracking-tight">Teslimat Bilgileri</h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-[100px_1fr]">
                        <span class="text-muted-foreground">Adres:</span>
                        <span class="font-medium">{{ $order->shipping_address }}<br>{{ $order->shipping_district }}, {{ $order->shipping_city }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card>
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="font-semibold tracking-tight">Sipariş İçeriği</h3>
                <span class="text-sm text-muted-foreground">{{ $order->items->count() }} ürün</span>
            </div>
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Ürün</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Adet</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Birim Fiyat</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Toplam</th>
                    </tr>
                </x-slot>

                @foreach($order->items as $item)
                    <tr class="border-b transition-colors hover:bg-muted/30">
                        <td class="p-4 px-6 align-middle font-medium">{{ $item->name }}</td>
                        <td class="p-4 px-6 align-middle text-right">{{ $item->quantity }}</td>
                        <td class="p-4 px-6 align-middle text-right">{{ number_format($item->unit_price_cents / 100, 2, ',', '.') }} ₺</td>
                        <td class="p-4 px-6 align-middle text-right font-medium">{{ number_format($item->line_total_cents / 100, 2, ',', '.') }} ₺</td>
                    </tr>
                @endforeach
            </x-ui.table>
            <div class="p-6 border-t flex justify-end">
                <div class="space-y-2 w-full max-w-xs text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Ara Toplam</span>
                        <span>{{ number_format($order->total_cents / 100, 2, ',', '.') }} ₺</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Ödeme Durumu</span>
                        <span class="font-medium text-primary">{{ $order->payment?->status->value ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-4 border-t mt-4">
                        <span>Genel Toplam</span>
                        <span>{{ number_format($order->total_cents / 100, 2, ',', '.') }} ₺</span>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layouts.admin>
