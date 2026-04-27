<x-layouts.admin header="Kampanyalar & Kuponlar">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Kampanyalar & Kuponlar</h2>
                <p class="text-muted-foreground">Pazarlama kampanyalarınızı ve indirim kodlarınızı yönetin.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Create Campaign Form -->
            <x-ui.card>
                <div class="p-6 border-b">
                    <h3 class="font-semibold tracking-tight">Yeni Kampanya Ekle</h3>
                </div>
                <form action="{{ route('admin.campaigns.store') }}" method="POST">
                    @csrf
                    <div class="p-6 grid gap-4 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="name">Kampanya Adı *</x-ui.label>
                            <x-ui.input id="name" name="name" required placeholder="Örn: Yaz İndirimi" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="slug">URL Uzantısı (Slug)</x-ui.label>
                            <x-ui.input id="slug" name="slug" placeholder="yaz-indirimi" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="description">Açıklama</x-ui.label>
                            <x-ui.textarea id="description" name="description"></x-ui.textarea>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="discount_type">İndirim Tipi</x-ui.label>
                            <x-ui.select id="discount_type" name="discount_type">
                                <option value="percent">Yüzde (%)</option>
                                <option value="fixed">Sabit Tutar (₺)</option>
                            </x-ui.select>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="discount_value">İndirim Değeri *</x-ui.label>
                            <x-ui.input id="discount_value" name="discount_value" type="number" min="0" value="0" required />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="banner_image_url">Afiş / Görsel URL</x-ui.label>
                            <x-ui.input id="banner_image_url" name="banner_image_url" type="url" placeholder="https://..." />
                        </div>
                        <div class="flex items-center space-x-2 md:col-span-2 pt-2">
                            <x-ui.checkbox id="campaign_is_active" name="is_active" value="1" checked />
                            <x-ui.label for="campaign_is_active" class="cursor-pointer">Kampanya aktif edilsin</x-ui.label>
                        </div>
                    </div>
                    <div class="p-6 border-t bg-muted/20">
                        <x-ui.button type="submit" class="w-full">Kampanya Oluştur</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <!-- Create Coupon Form -->
            <x-ui.card>
                <div class="p-6 border-b">
                    <h3 class="font-semibold tracking-tight">Yeni Kupon Ekle</h3>
                </div>
                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf
                    <div class="p-6 grid gap-4 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="campaign_id">Bağlı Kampanya (İsteğe Bağlı)</x-ui.label>
                            <x-ui.select id="campaign_id" name="campaign_id">
                                <option value="">Bağımsız Kupon (Kampanyasız)</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="code">Kupon Kodu *</x-ui.label>
                            <x-ui.input id="code" name="code" required placeholder="Örn: YAZ2026" class="uppercase" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="coupon_discount_type">İndirim Tipi</x-ui.label>
                            <x-ui.select id="coupon_discount_type" name="discount_type">
                                <option value="percent">Yüzde (%)</option>
                                <option value="fixed">Sabit Tutar (₺)</option>
                            </x-ui.select>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="coupon_discount_value">İndirim Değeri *</x-ui.label>
                            <x-ui.input id="coupon_discount_value" name="discount_value" type="number" min="0" value="0" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="minimum_order_cents">Minimum Sepet Tutarı (Kuruş)</x-ui.label>
                            <x-ui.input id="minimum_order_cents" name="minimum_order_cents" type="number" min="0" value="0" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="usage_limit">Kullanım Limiti</x-ui.label>
                            <x-ui.input id="usage_limit" name="usage_limit" type="number" min="1" placeholder="Limitsiz için boş bırakın" />
                        </div>
                        <div class="flex items-center space-x-2 md:col-span-2 pt-2">
                            <x-ui.checkbox id="coupon_is_active" name="is_active" value="1" checked />
                            <x-ui.label for="coupon_is_active" class="cursor-pointer">Kupon aktif edilsin</x-ui.label>
                        </div>
                    </div>
                    <div class="p-6 border-t bg-muted/20">
                        <x-ui.button type="submit" class="w-full">Kupon Oluştur</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <x-ui.card>
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="font-semibold tracking-tight">Aktif Kampanyalar</h3>
            </div>
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Kampanya</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İndirim</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Bağlı Kuponlar</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                    </tr>
                </x-slot>

                @forelse($campaigns as $campaign)
                    <tr class="border-b transition-colors hover:bg-muted/30">
                        <td class="p-4 px-6 align-middle">
                            <div class="font-medium">{{ $campaign->name }}</div>
                            <div class="text-xs text-muted-foreground">{{ $campaign->slug }}</div>
                        </td>
                        <td class="p-4 px-6 align-middle font-medium text-sm">
                            @if($campaign->discount_type === 'percent')
                                %{{ $campaign->discount_value }}
                            @else
                                {{ number_format($campaign->discount_value / 100, 2, ',', '.') }} ₺
                            @endif
                        </td>
                        <td class="p-4 px-6 align-middle text-sm">
                            @if($campaign->coupons->count())
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($campaign->coupons as $coupon)
                                        <x-ui.badge variant="secondary" class="font-mono">{{ $coupon->code }}</x-ui.badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="p-4 px-6 align-middle">
                            @if($campaign->is_active)
                                <x-ui.badge variant="default" class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-emerald-500/20">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Pasif</x-ui.badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-muted-foreground">Henüz bir kampanya eklenmemiş.</td>
                    </tr>
                @endforelse
            </x-ui.table>
            @if($campaigns->hasPages())
                <div class="p-4 px-6 border-t bg-muted/20">
                    {{ $campaigns->links('pagination::tailwind') }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
