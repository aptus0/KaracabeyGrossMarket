<x-layouts.admin header="Ödemeler">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Ödemeler (PayTR)</h2>
                <p class="text-muted-foreground">İşlemleri ve PayTR entegrasyon loglarını inceleyin.</p>
            </div>
            <x-ui.button as="a" href="#" variant="outline">
                <x-lucide-download class="mr-2 h-4 w-4" /> Rapor İndir
            </x-ui.button>
        </div>

        <x-ui.card>
            <div class="p-6 pb-0 border-b pb-6 flex items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                    <x-ui.input type="search" name="q" placeholder="Sipariş Numarası (OID) ile arayın..." class="pl-9 bg-muted/50" />
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.select class="w-[180px]">
                        <option value="">Tüm Durumlar</option>
                        <option value="paid">Ödendi</option>
                        <option value="failed">Başarısız</option>
                        <option value="refunded">İade Edildi</option>
                    </x-ui.select>
                </div>
            </div>
            
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Sipariş OID</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Müşteri</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tutar</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İadeler</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Tarih</th>
                    </tr>
                </x-slot>

                @forelse($payments as $payment)
                    <tr class="border-b transition-colors hover:bg-muted/30">
                        <td class="p-4 px-6 align-middle font-mono text-sm font-medium">{{ $payment->merchant_oid }}</td>
                        <td class="p-4 px-6 align-middle">
                            <div class="font-medium text-sm">{{ $payment->order?->customer_name ?? '-' }}</div>
                        </td>
                        <td class="p-4 px-6 align-middle font-semibold text-sm">
                            {{ number_format($payment->amount_cents / 100, 2, ',', '.') }} {{ $payment->currency }}
                        </td>
                        <td class="p-4 px-6 align-middle">
                            @if($payment->status->value === 'paid')
                                <x-ui.badge variant="default" class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-emerald-500/20">Ödendi</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">{{ $payment->status->value }}</x-ui.badge>
                            @endif
                        </td>
                        <td class="p-4 px-6 align-middle text-sm">
                            {{ number_format($payment->refunds->where('status', 'success')->sum('amount_cents') / 100, 2, ',', '.') }} ₺
                        </td>
                        <td class="p-4 px-6 align-middle text-right text-sm text-muted-foreground">
                            {{ $payment->created_at?->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">Ödeme kaydı bulunamadı.</td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if($payments->hasPages())
                <div class="p-4 px-6 border-t bg-muted/20">
                    {{ $payments->links('pagination::tailwind') }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
