@extends('admin.layout')

@section('title', 'Urunler')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Katalog</p>
            <h1>Urunler</h1>
        </div>
        <div class="actions">
            <form method="get">
                <input name="q" value="{{ request('q') }}" placeholder="Urun ara">
            </form>
            <a class="btn primary" href="{{ route('admin.products.create') }}">Yeni Urun</a>
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Urun</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}<br><small>{{ $product->slug }}</small></td>
                        <td>{{ $product->categories->pluck('name')->join(', ') ?: '-' }}</td>
                        <td>{{ number_format($product->price_cents / 100, 2, ',', '.') }} TL</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->is_active ? 'Aktif' : 'Pasif' }}</td>
                        <td><a class="btn" href="{{ route('admin.products.edit', $product) }}">Duzenle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Urun yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $products->links() }}</div>
    </section>
@endsection
