<x-layouts.admin header="Ürünler">
    <div class="flex flex-col gap-6" x-data="bulkSelect()">

        {{-- Başlık --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Ürünler</h2>
                <p class="text-muted-foreground">Katalogdaki ürünlerinizi buradan yönetebilirsiniz.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Sıfır Stok Toplu Pasif --}}
                <form method="POST" action="{{ route('admin.products.bulk') }}"
                      onsubmit="return confirm('Stok adedi 0 olan TÜM aktif ürünler pasif edilecek. Onaylıyor musunuz?')">
                    @csrf
                    <input type="hidden" name="action" value="deactivate_zero_stock">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100 transition-colors">
                        <x-lucide-archive-x class="h-4 w-4" />
                        Sıfır Stokluları Pasif Et
                    </button>
                </form>

                <x-ui.button as="a" href="{{ route('admin.products.create') }}">
                    <x-lucide-plus class="mr-2 h-4 w-4" /> Ürün Ekle
                </x-ui.button>
            </div>
        </div>

        {{-- Durum mesajı --}}
        @if(session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                ✓ {{ session('status') }}
            </div>
        @endif

        {{-- Toplu İşlem Araç Çubuğu --}}
        <div x-show="selectedIds.length > 0" x-cloak
             class="flex items-center gap-3 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3">
            <span class="text-sm font-medium text-orange-700">
                <span x-text="selectedIds.length"></span> ürün seçildi
            </span>
            <div class="ml-auto flex items-center gap-2">
                <form method="POST" action="{{ route('admin.products.bulk') }}" @submit="appendIds($event, 'activate')">
                    @csrf
                    <input type="hidden" name="action" value="activate">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 transition-colors">
                        <x-lucide-check class="h-3.5 w-3.5" /> Aktif Et
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.products.bulk') }}" @submit="appendIds($event, 'deactivate')">
                    @csrf
                    <input type="hidden" name="action" value="deactivate">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-md bg-slate-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                        <x-lucide-eye-off class="h-3.5 w-3.5" /> Pasif Et
                    </button>
                </form>
                <button @click="clearAll()" class="text-xs text-slate-500 hover:text-slate-700 px-2">İptal</button>
            </div>
        </div>

        <x-ui.card>
            {{-- Arama --}}
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
                        <th class="h-12 px-4 text-left align-middle w-10">
                            <input type="checkbox" id="select-all"
                                   class="rounded border-slate-300"
                                   @change="toggleAll($event)"
                                   :checked="allSelected">
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">ID</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Ürün</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Marka</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Fiyat</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Stok</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İşlemler</th>
                    </tr>
                </x-slot>

                @forelse($products as $product)
                    <tr class="border-b transition-colors hover:bg-muted/30 group"
                        :class="selectedIds.includes({{ $product->id }}) ? 'bg-orange-50' : ''">
                        <td class="p-3 px-4 align-middle">
                            <input type="checkbox"
                                   class="rounded border-slate-300 product-checkbox"
                                   value="{{ $product->id }}"
                                   @change="toggle({{ $product->id }})"
                                   :checked="selectedIds.includes({{ $product->id }})">
                        </td>
                        <td class="p-3 px-4 align-middle font-mono text-xs text-muted-foreground">{{ $product->id }}</td>
                        <td class="p-3 px-4 align-middle">
                            <div class="font-semibold text-sm">{{ $product->name }}</div>
                            <div class="text-xs text-muted-foreground mt-0.5 font-mono">{{ $product->barcode }}</div>
                        </td>
                        <td class="p-3 px-4 align-middle text-sm">{{ $product->brand ?? '-' }}</td>
                        <td class="p-3 px-4 align-middle font-semibold text-sm">{{ $product->formattedPrice() }}</td>
                        <td class="p-3 px-4 align-middle text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $product->stock_quantity > 0
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : 'bg-red-100 text-red-800' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td class="p-3 px-4 align-middle">
                            @if($product->is_active)
                                <x-ui.badge variant="default">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Pasif</x-ui.badge>
                            @endif
                        </td>
                        <td class="p-3 px-4 align-middle text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <x-ui.button variant="ghost" size="icon" as="a" href="{{ route('admin.products.edit', $product) }}">
                                <x-lucide-pencil class="h-4 w-4" />
                            </x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-muted-foreground">
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

    @push('scripts')
    <script>
    function bulkSelect() {
        return {
            selectedIds: [],
            get allSelected() {
                const boxes = document.querySelectorAll('.product-checkbox');
                return boxes.length > 0 && this.selectedIds.length === boxes.length;
            },
            toggle(id) {
                const idx = this.selectedIds.indexOf(id);
                if (idx === -1) this.selectedIds.push(id);
                else this.selectedIds.splice(idx, 1);
            },
            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedIds = Array.from(document.querySelectorAll('.product-checkbox')).map(el => parseInt(el.value));
                } else {
                    this.selectedIds = [];
                }
            },
            clearAll() { this.selectedIds = []; },
            appendIds(event, action) {
                const form = event.target;
                this.selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
            }
        }
    }
    </script>
    @endpush
</x-layouts.admin>