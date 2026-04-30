<x-layouts.admin header="Hikayeler (Stories)">
    <div class="flex flex-col gap-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Hikayeler (Stories)</h2>
                <p class="text-muted-foreground">Mobil anasayfanın üst kısmında görünen hikayeleri yönetin.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Form Column --}}
            <div class="lg:col-span-1">
                <x-ui.card>
                    <div class="p-6 border-b">
                        <h3 class="font-semibold tracking-tight">Yeni Hikaye Ekle</h3>
                    </div>
                    <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="p-6 grid gap-4">
                            <div class="space-y-2">
                                <x-ui.label for="title">Başlık *</x-ui.label>
                                <x-ui.input id="title" name="title" required placeholder="Örn: Hafta Sonu Fırsatı" maxlength="120" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="subtitle">Alt Başlık</x-ui.label>
                                <x-ui.input id="subtitle" name="subtitle" placeholder="Kısa açıklama..." maxlength="240" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="image">Görsel Yükle (Kare önerilir) *</x-ui.label>
                                <x-ui.input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" required />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="category_slug">Kategori Bağlantısı</x-ui.label>
                                <x-ui.input id="category_slug" name="category_slug" placeholder="Örn: meyve-sebze" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="custom_url">Özel URL (Varsa)</x-ui.label>
                                <x-ui.input id="custom_url" name="custom_url" type="url" placeholder="https://..." />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <x-ui.label for="gradient_start">Gradient Başlangıç</x-ui.label>
                                    <input type="color" name="gradient_start" id="gradient_start" value="#FF7A00" class="h-10 w-full rounded-lg border cursor-pointer" />
                                </div>
                                <div class="space-y-2">
                                    <x-ui.label for="gradient_end">Gradient Bitiş</x-ui.label>
                                    <input type="color" name="gradient_end" id="gradient_end" value="#FF3300" class="h-10 w-full rounded-lg border cursor-pointer" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="icon">İkon Adı (SF Symbol)</x-ui.label>
                                <x-ui.input id="icon" name="icon" value="tag.fill" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label for="sort_order">Sıralama</x-ui.label>
                                <x-ui.input id="sort_order" name="sort_order" type="number" min="0" value="0" />
                            </div>
                            <div class="flex items-center space-x-2 pt-2">
                                <x-ui.checkbox id="is_active" name="is_active" value="1" checked />
                                <x-ui.label for="is_active" class="cursor-pointer">Aktif olarak yayınla</x-ui.label>
                            </div>
                        </div>
                        <div class="p-6 border-t bg-muted/20">
                            <x-ui.button type="submit" class="w-full">Hikaye Ekle</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            {{-- List Column --}}
            <div class="lg:col-span-2">
                <x-ui.card>
                    <div class="p-6 border-b flex items-center justify-between">
                        <h3 class="font-semibold tracking-tight">Aktif Hikayeler</h3>
                        <span class="text-sm text-muted-foreground">{{ $stories->count() }} hikaye</span>
                    </div>
                    <div class="divide-y">
                        @forelse($stories as $story)
                        <div class="flex items-center gap-4 p-4">
                            @if($story->image_url)
                            <div class="h-16 w-16 rounded-full shrink-0 p-1" style="background: linear-gradient(135deg, {{ $story->gradient_start }}, {{ $story->gradient_end }})">
                                <img src="{{ $story->image_url }}" alt="{{ $story->title }}" class="h-full w-full rounded-full object-cover border-2 border-white" />
                            </div>
                            @else
                            <div class="h-16 w-16 rounded-full shrink-0 flex items-center justify-center text-white text-xl p-1" style="background: linear-gradient(135deg, {{ $story->gradient_start }}, {{ $story->gradient_end }})">
                                <div class="h-full w-full rounded-full border-2 border-white flex items-center justify-center bg-black/10">#</div>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <strong class="truncate text-sm">{{ $story->title }}</strong>
                                    @if($story->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">Pasif</span>
                                    @endif
                                </div>
                                @if($story->subtitle)
                                <p class="text-xs text-muted-foreground mt-0.5 truncate">{{ $story->subtitle }}</p>
                                @endif
                                <p class="text-xs text-muted-foreground mt-1">
                                    Sıra: {{ $story->sort_order }} | Hedef: {{ $story->category_slug ?? $story->custom_url ?? 'Yok' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <form action="{{ route('admin.stories.destroy', $story) }}" method="POST" onsubmit="return confirm('Hikayeyi silmek istediğinize emin misiniz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-red-200 px-3 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-sm text-muted-foreground">Henüz hikaye eklenmedi.</div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.admin>
