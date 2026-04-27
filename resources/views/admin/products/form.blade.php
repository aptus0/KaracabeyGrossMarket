<x-layouts.admin :header="$product->exists ? 'Edit Product' : 'Add Product'">
    <div class="flex flex-col gap-6 max-w-4xl mx-auto w-full">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ $product->exists ? 'Edit Product' : 'Add New Product' }}</h2>
                <p class="text-muted-foreground">{{ $product->exists ? 'Update the details of your product.' : 'Create a new product in your catalog.' }}</p>
            </div>
            <x-ui.button as="a" href="{{ route('admin.products.index') }}" variant="outline">
                <x-lucide-arrow-left class="mr-2 h-4 w-4" /> Back to Products
            </x-ui.button>
        </div>

        <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST">
            @csrf
            @if($product->exists)
                @method('PUT')
            @endif

            <div class="grid gap-6">
                <!-- General Information -->
                <x-ui.card>
                    <div class="p-6 pb-0 flex flex-col space-y-1.5 border-b pb-4 mb-4">
                        <h3 class="font-semibold leading-none tracking-tight">General Information</h3>
                        <p class="text-sm text-muted-foreground">Product name, description and categorisation.</p>
                    </div>
                    <div class="p-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="name">Product Name *</x-ui.label>
                            <x-ui.input id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="e.g. Gunluk Sut 1 L" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="slug">Slug</x-ui.label>
                            <x-ui.input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Leave empty to auto-generate" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="description">Description</x-ui.label>
                            <x-ui.textarea id="description" name="description" placeholder="Detailed product description...">{{ old('description', $product->description) }}</x-ui.textarea>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="brand">Brand</x-ui.label>
                            <x-ui.input id="brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="e.g. KGM" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="categories">Categories</x-ui.label>
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

                <!-- Pricing & Inventory -->
                <x-ui.card>
                    <div class="p-6 pb-0 flex flex-col space-y-1.5 border-b pb-4 mb-4">
                        <h3 class="font-semibold leading-none tracking-tight">Pricing & Inventory</h3>
                        <p class="text-sm text-muted-foreground">Manage prices, barcode, and stock availability.</p>
                    </div>
                    <div class="p-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <x-ui.label for="price_cents">Price (in cents) *</x-ui.label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-muted-foreground">₺</span>
                                <x-ui.input id="price_cents" name="price_cents" type="number" min="0" value="{{ old('price_cents', $product->price_cents ?? 0) }}" class="pl-8" required />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="compare_at_price_cents">Compare at Price (in cents)</x-ui.label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-muted-foreground">₺</span>
                                <x-ui.input id="compare_at_price_cents" name="compare_at_price_cents" type="number" min="0" value="{{ old('compare_at_price_cents', $product->compare_at_price_cents) }}" class="pl-8" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="stock_quantity">Stock Quantity *</x-ui.label>
                            <x-ui.input id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="barcode">Barcode (SKU/EAN)</x-ui.label>
                            <x-ui.input id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="e.g. 8690000000000" />
                        </div>
                    </div>
                </x-ui.card>

                <!-- Media & SEO -->
                <x-ui.card>
                    <div class="p-6 pb-0 flex flex-col space-y-1.5 border-b pb-4 mb-4">
                        <h3 class="font-semibold leading-none tracking-tight">Media & SEO</h3>
                        <p class="text-sm text-muted-foreground">Images and search engine optimization settings.</p>
                    </div>
                    <div class="p-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="image_url">Main Image URL</x-ui.label>
                            <x-ui.input id="image_url" name="image_url" type="url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..." />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="seo_title">SEO Title</x-ui.label>
                            <x-ui.input id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo['title'] ?? '') }}" placeholder="Meta Title" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="seo_description">SEO Description</x-ui.label>
                            <x-ui.textarea id="seo_description" name="seo_description" placeholder="Meta Description">{{ old('seo_description', $product->seo['description'] ?? '') }}</x-ui.textarea>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Actions -->
                <div class="flex items-center justify-between border-t pt-6 pb-12">
                    <div class="flex items-center space-x-2">
                        <x-ui.checkbox id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) />
                        <x-ui.label for="is_active" class="cursor-pointer">Product is active and visible</x-ui.label>
                    </div>
                    <div class="flex gap-4">
                        <x-ui.button type="button" variant="ghost" as="a" href="{{ route('admin.products.index') }}">Cancel</x-ui.button>
                        <x-ui.button type="submit">
                            <x-lucide-save class="mr-2 h-4 w-4" /> Save Product
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
