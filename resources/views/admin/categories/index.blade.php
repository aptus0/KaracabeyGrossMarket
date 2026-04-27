<x-layouts.admin header="Kategoriler">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Kategoriler</h2>
                <p class="text-muted-foreground">Ürün kataloğunuzun kategorilerini yönetin.</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="md:col-span-1">
                <x-ui.card>
                    <div class="p-6 border-b">
                        <h3 class="font-semibold tracking-tight">Yeni Kategori Ekle</h3>
                    </div>
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div class="space-y-2">
                                <x-ui.label for="name">Kategori Adı *</x-ui.label>
                                <x-ui.input id="name" name="name" required />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="slug">URL Uzantısı (Slug)</x-ui.label>
                                <x-ui.input id="slug" name="slug" placeholder="Otomatik oluşturulur" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="sort_order">Sıra</x-ui.label>
                                <x-ui.input id="sort_order" name="sort_order" type="number" min="0" value="0" />
                            </div>
                            <div class="flex items-center space-x-2 pt-2">
                                <x-ui.checkbox id="is_active" name="is_active" value="1" checked />
                                <x-ui.label for="is_active" class="cursor-pointer">Aktif</x-ui.label>
                            </div>
                        </div>
                        <div class="p-6 border-t bg-muted/20">
                            <x-ui.button type="submit" class="w-full">Kategori Ekle</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            <div class="md:col-span-2">
                <x-ui.card>
                    <x-ui.table>
                        <x-slot name="header">
                            <tr>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Kategori Adı</th>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Sıra</th>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Durum</th>
                            </tr>
                        </x-slot>

                        @forelse($categories as $category)
                            <tr class="border-b transition-colors hover:bg-muted/30">
                                <td class="p-4 px-6 align-middle">
                                    <div class="font-medium">{{ $category->name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $category->slug }}</div>
                                </td>
                                <td class="p-4 px-6 align-middle">{{ $category->sort_order }}</td>
                                <td class="p-4 px-6 align-middle">
                                    @if($category->is_active)
                                        <x-ui.badge variant="default">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="secondary">Pasif</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-muted-foreground">Kategori bulunamadı.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>

                    @if($categories->hasPages())
                        <div class="p-4 px-6 border-t bg-muted/20">
                            {{ $categories->links('pagination::tailwind') }}
                        </div>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.admin>
