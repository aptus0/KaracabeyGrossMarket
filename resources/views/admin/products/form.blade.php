<x-layouts.admin :header="$product->exists ? 'Ürün Düzenle' : 'Yeni Ürün Ekle'">
    <div class="flex flex-col gap-6 max-w-4xl mx-auto w-full">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">{{ $product->exists ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' }}</h2>
                <p class="text-sm text-slate-500">{{ $product->exists ? 'Ürünün bilgilerini güncelleyin.' : 'Kataloga yeni bir ürün ekleyin.' }}</p>
            </div>
            <x-ui.button as="a" href="{{ route('admin.products.index') }}" variant="outline" class="rounded-md">
                <x-lucide-arrow-left class="mr-2 h-4 w-4" /> Ürünlere Dön
            </x-ui.button>
        </div>

        <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @if($product->exists)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-5">
                {{-- Genel Bilgiler --}}
                <x-ui.card class="rounded-lg">
                    <div class="border-b px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-800">Genel Bilgiler</h3>
                    </div>
                    <div class="p-6 grid gap-5 md:grid-cols-2">
                        <div class="space-y-1.5 md:col-span-2">
                            <x-ui.label for="name">Ürün Adı *</x-ui.label>
                            <x-ui.input id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="Örn: Günlük Süt 1 L" />
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <x-ui.label for="slug">URL (Slug)</x-ui.label>
                            <x-ui.input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Boş bırakırsanız otomatik oluşturulur" />
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <x-ui.label for="description">Açıklama</x-ui.label>
                            <x-ui.textarea id="description" name="description" placeholder="Ürün hakkında detaylı açıklama...">{{ old('description', $product->description) }}</x-ui.textarea>
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="brand">Marka</x-ui.label>
                            <x-ui.input id="brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Örn: KGM" />
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="categories">Kategoriler</x-ui.label>
                            <x-ui.select id="categories" name="category_ids[]" multiple class="h-24">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', $product->categories->pluck('id')->all())))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>
                    </div>
                </x-ui.card>

                {{-- Fiyat & Stok --}}
                <x-ui.card class="rounded-lg">
                    <div class="border-b px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-800">Fiyat & Stok</h3>
                    </div>
                    <div class="p-6 grid gap-5 md:grid-cols-2">
                        <div class="space-y-1.5">
                            <x-ui.label for="price_cents">Satış Fiyatı (kuruş) *</x-ui.label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">₺</span>
                                <x-ui.input id="price_cents" name="price_cents" type="number" min="0" value="{{ old('price_cents', $product->price_cents ?? 0) }}" class="pl-8" required />
                            </div>
                            <p class="text-xs text-slate-400">1 TL = 100 kuruş. Örn: 29,90 ₺ için 2990 girin.</p>
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="compare_at_price_cents">Karşılaştırma Fiyatı (kuruş)</x-ui.label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">₺</span>
                                <x-ui.input id="compare_at_price_cents" name="compare_at_price_cents" type="number" min="0" value="{{ old('compare_at_price_cents', $product->compare_at_price_cents) }}" class="pl-8" />
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="stock_quantity">Stok Adedi *</x-ui.label>
                            <x-ui.input id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required />
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="barcode">Barkod (SKU / EAN)</x-ui.label>
                            <x-ui.input id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Örn: 8690000000000" />
                        </div>
                    </div>
                </x-ui.card>

                {{-- Ürün Görseli --}}
                <x-ui.card class="rounded-lg">
                    <div class="border-b px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-800">Ürün Görseli</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Mevcut görsel önizleme --}}
                        @if($product->image_url)
                            <div class="flex items-center gap-4 rounded-md border border-slate-200 bg-slate-50 p-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="h-20 w-20 rounded-md object-cover border border-slate-200">
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Mevcut Görsel</p>
                                    <p class="mt-0.5 text-xs text-slate-400 break-all">{{ $product->image_url }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Dosya yükleme alanı --}}
                        <div x-data="imageUpload()" class="space-y-3">
                            <label
                                for="image_file"
                                class="flex flex-col items-center justify-center w-full h-36 rounded-md border-2 border-dashed border-slate-300 bg-slate-50 cursor-pointer transition hover:border-orange-400 hover:bg-orange-50"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="onDrop($event)"
                                :class="dragging ? 'border-orange-400 bg-orange-50' : ''"
                            >
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center gap-2 text-slate-400">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="text-sm font-medium">Görsel yüklemek için tıklayın veya sürükleyin</p>
                                        <p class="text-xs">JPG, PNG, WEBP — maks. 4 MB</p>
                                    </div>
                                </template>
                                <template x-if="preview">
                                    <img :src="preview" class="h-full w-auto max-w-full rounded object-contain p-2">
                                </template>
                                <input id="image_file" name="image_file" type="file"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="hidden"
                                       @change="onFileChange($event)">
                            </label>

                            <template x-if="preview">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-emerald-600 font-medium">✓ Yeni görsel seçildi</span>
                                    <button type="button" @click="clearPreview()" class="text-xs text-rose-500 hover:underline">Kaldır</button>
                                </div>
                            </template>
                        </div>

                        {{-- Alternatif: URL ile yükleme --}}
                        <div class="space-y-1.5">
                            <x-ui.label for="image_url" class="text-xs text-slate-500">Veya görsel URL girin (opsiyonel)</x-ui.label>
                            <x-ui.input id="image_url" name="image_url" type="url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..." />
                            <p class="text-xs text-slate-400">Dosya yükleme önceliklidir. Her ikisi doluysa yüklenen dosya kullanılır.</p>
                        </div>
                    </div>
                </x-ui.card>

                {{-- SEO --}}
                <x-ui.card class="rounded-lg">
                    <div class="border-b px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-800">SEO Ayarları</h3>
                    </div>
                    <div class="p-6 grid gap-5 md:grid-cols-2">
                        <div class="space-y-1.5 md:col-span-2">
                            <x-ui.label for="seo_title">SEO Başlığı</x-ui.label>
                            <x-ui.input id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo['title'] ?? '') }}" placeholder="Meta Title" />
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <x-ui.label for="seo_description">SEO Açıklaması</x-ui.label>
                            <x-ui.textarea id="seo_description" name="seo_description" placeholder="Meta Description">{{ old('seo_description', $product->seo['description'] ?? '') }}</x-ui.textarea>
                        </div>
                    </div>
                </x-ui.card>

                {{-- Kaydet --}}
                <div class="flex items-center justify-between border-t pt-5 pb-12">
                    <div class="flex items-center gap-2">
                        <x-ui.checkbox id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) />
                        <x-ui.label for="is_active" class="cursor-pointer">Ürün aktif ve görünür</x-ui.label>
                    </div>
                    <div class="flex gap-3">
                        <x-ui.button type="button" variant="ghost" as="a" href="{{ route('admin.products.index') }}">İptal</x-ui.button>
                        <x-ui.button type="submit" class="rounded-md">
                            <x-lucide-save class="mr-2 h-4 w-4" /> Ürünü Kaydet
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function imageUpload() {
            return {
                preview: null,
                dragging: false,
                onFileChange(e) {
                    const file = e.target.files[0];
                    if (file) this.preview = URL.createObjectURL(file);
                },
                onDrop(e) {
                    this.dragging = false;
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const input = document.getElementById('image_file');
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        this.preview = URL.createObjectURL(file);
                    }
                },
                clearPreview() {
                    this.preview = null;
                    const input = document.getElementById('image_file');
                    if (input) input.value = '';
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>

