@extends('admin.layout')

@section('title', $product->exists ? 'Urun Duzenle' : 'Yeni Urun')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Katalog</p>
            <h1>{{ $product->exists ? 'Urun Duzenle' : 'Yeni Urun' }}</h1>
        </div>
        <a class="btn" href="{{ route('admin.products.index') }}">Listeye Don</a>
    </div>

    <section class="panel">
        <form class="form" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="post">
            @csrf
            @if($product->exists)
                @method('put')
            @endif

            <label>Ad
                <input name="name" value="{{ old('name', $product->name) }}" required>
            </label>
            <label>Slug
                <input name="slug" value="{{ old('slug', $product->slug) }}">
            </label>
            <label>Aciklama
                <textarea name="description">{{ old('description', $product->description) }}</textarea>
            </label>
            <label>Marka
                <input name="brand" value="{{ old('brand', $product->brand) }}">
            </label>
            <label>Barkod
                <input name="barcode" value="{{ old('barcode', $product->barcode) }}">
            </label>
            <label>Fiyat kurus
                <input name="price_cents" type="number" min="0" value="{{ old('price_cents', $product->price_cents ?? 0) }}" required>
            </label>
            <label>Karsilastirma fiyati kurus
                <input name="compare_at_price_cents" type="number" min="0" value="{{ old('compare_at_price_cents', $product->compare_at_price_cents) }}">
            </label>
            <label>Stok
                <input name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
            </label>
            <label>Gorsel URL
                <input name="image_url" type="url" value="{{ old('image_url', $product->image_url) }}">
            </label>
            <label>SEO basligi
                <input name="seo_title" value="{{ old('seo_title', $product->seo['title'] ?? '') }}">
            </label>
            <label>SEO aciklamasi
                <textarea name="seo_description">{{ old('seo_description', $product->seo['description'] ?? '') }}</textarea>
            </label>
            <label>Meta gorsel URL
                <input name="meta_image_url" type="url" value="{{ old('meta_image_url', $product->seo['image_url'] ?? '') }}">
            </label>
            <label>Kategoriler
                <select name="category_ids[]" multiple>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', $product->categories->pluck('id')->all())))>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))>
                Aktif
            </label>
            <button class="btn primary" type="submit">Kaydet</button>
        </form>
    </section>
@endsection
