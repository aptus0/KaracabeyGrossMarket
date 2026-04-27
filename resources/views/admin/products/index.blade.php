@extends('admin.layout')

@section('title', 'Ürünler')

@section('content')
    <style>
        /* shadcn/ui Uyumlu Sayfa Stilleri */
        .top { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; gap: 1rem; }
        .top h1 { font-size: 1.875rem; font-weight: 700; letter-spacing: -0.025em; color: var(--foreground); margin: 0; line-height: 1.2; }
        .eyebrow { font-size: 0.875rem; font-weight: 500; color: var(--muted-foreground); margin: 0 0 0.25rem 0; text-transform: none; }
        
        /* Aksiyon Alanı ve Arama Kutusu */
        .actions { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .actions form { margin: 0; display: flex; }
        .actions input { 
            height: 2.5rem; border-radius: var(--radius); border: 1px solid var(--border); 
            background: var(--sidebar-bg); padding: 0 0.75rem; font-size: 0.875rem; 
            outline: none; transition: all 0.2s; min-width: 220px; color: var(--foreground); 
        }
        .actions input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15); }
        .actions input::placeholder { color: var(--muted-foreground); }
        
        /* Butonlar */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; height: 2.5rem; padding: 0 1rem; font-size: 0.875rem; font-weight: 500; border-radius: var(--radius); transition: all 0.2s; cursor: pointer; border: 1px solid var(--border); background: var(--sidebar-bg); color: var(--foreground); }
        .btn:hover { background: var(--accent); color: var(--accent-foreground); }
        .btn.primary { background: var(--primary); color: white; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .btn.primary:hover { background: var(--primary); opacity: 0.9; }

        /* Tablo Paneli */
        .panel { background: var(--sidebar-bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem; }
        th { font-weight: 500; color: var(--muted-foreground); padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); color: var(--foreground); vertical-align: middle; }
        tbody tr:hover { background: var(--accent); }
        tbody tr:last-child td { border-bottom: none; }
        td small { color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 2px; }

        /* Durum Rozetleri */
        .badge { display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; }
        .badge-active { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-inactive { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Sayfalama Alanı */
        .pagination { padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; align-items: center; }
    </style>

    <div class="top">
        <div>
            <p class="eyebrow">Katalog</p>
            <h1>Ürünler</h1>
        </div>
        <div class="actions">
            <form method="get">
                <input name="q" value="{{ request('q') }}" placeholder="Ürün ara...">
            </form>
            <a class="btn primary" href="{{ route('admin.products.create') }}">Yeni Ürün</a>
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Ürün</th>
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
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <small>{{ $product->slug }}</small>
                        </td>
                        <td>{{ $product->categories->pluck('name')->join(', ') ?: '-' }}</td>
                        <td>{{ number_format($product->price_cents / 100, 2, ',', '.') }} TL</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-active">Aktif</span>
                            @else
                                <span class="badge badge-inactive">Pasif</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a class="btn" href="{{ route('admin.products.edit', $product) }}">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
                            Ürün bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($products->hasPages())
            <div class="pagination">
                {{ $products->links() }}
            </div>
        @endif
    </section>
@endsection