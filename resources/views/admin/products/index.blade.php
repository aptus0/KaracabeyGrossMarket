<x-layouts.admin header="Ürünler">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Ürünler</h2>
                <p class="text-muted-foreground">Katalogdaki ürünlerinizi buradan yönetebilirsiniz.</p>
            </div>
            <x-ui.button as="a" href="{{ route('admin.products.create') }}">
                <x-lucide-plus class="mr-2 h-4 w-4" /> Ürün Ekle
            </x-ui.button>
        </div>

        <x-ui.card>
            <div class="p-6 pb-0 border-b pb-6">
                <form action="{{ route('admin.products.index') }}" method="GET" class="flex items-center gap-4 max-w-md">
                    <div class="relative flex-1">
                        <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                        <x-ui.input type="search" name="q" placeholder="İsim, SKU veya barkod ile arayın..." class="pl-9 bg-muted/50" value="{{ request('q') }}" />
                    </div>
                    <x-ui.button type="submit" variant="secondary">Ara</x-ui.button>
                </form>
            </div>
            
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">ID</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Ürün</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Marka</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Fiyat</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Stok</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İşlemler</th>
                    </tr>
                </x-slot>

                @forelse($products as $product)
                    <tr class="border-b transition-colors hover:bg-muted/30 data-[state=selected]:bg-muted group">
                        <td class="p-4 px-6 align-middle font-mono text-xs text-muted-foreground">{{ $product->id }}</td>
                        <td class="p-4 px-6 align-middle">
                            <div class="font-semibold text-sm">{{ $product->name }}</div>
                            <div class="text-xs text-muted-foreground mt-0.5">{{ $product->slug }}</div>
                        </td>
                        <td class="p-4 px-6 align-middle text-sm">{{ $product->brand ?? '-' }}</td>
                        <td class="p-4 px-6 align-middle font-semibold text-sm">{{ $product->formattedPrice() }}</td>
                        <td class="p-4 px-6 align-middle text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->stock_quantity > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td class="p-4 px-6 align-middle">
                            @if($product->is_active)
                                <x-ui.badge variant="default">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Pasif</x-ui.badge>
                            @endif
                        </td>
                        <td class="p-4 px-6 align-middle text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <x-ui.button variant="ghost" size="icon" as="a" href="{{ route('admin.products.edit', $product) }}">
                                <x-lucide-pencil class="h-4 w-4" />
                            </x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <x-lucide-package-x class="h-10 w-10 text-muted-foreground/50" />
                                <p class="text-lg font-medium">Ürün bulunamadı</p>
                                <p class="text-sm">Arama kriterlerinizi değiştirin.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4 px-6 border-t bg-muted/20">
                {{ $products->links('pagination::tailwind') }}
            </div>
        </x-ui.card>
    </div>
</x-layouts.admin>