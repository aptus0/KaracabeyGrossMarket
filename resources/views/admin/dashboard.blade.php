@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <style>
        /* Sadece Dashboard'a etki edecek shadcn/ui stili */
        .top { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; gap: 1rem; }
        .top h1 { font-size: 1.875rem; font-weight: 700; letter-spacing: -0.025em; color: var(--foreground); margin: 0; line-height: 1.2; }
        .eyebrow { font-size: 0.875rem; font-weight: 500; color: var(--muted-foreground); margin: 0 0 0.25rem 0; text-transform: none; }
        
        /* Butonlar */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; height: 2.5rem; padding: 0 1rem; font-size: 0.875rem; font-weight: 500; border-radius: var(--radius); transition: all 0.2s; cursor: pointer; border: 1px solid var(--border); background: var(--sidebar-bg); color: var(--foreground); }
        .btn:hover { background: var(--accent); color: var(--accent-foreground); }
        .btn.primary { background: var(--primary); color: white; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .btn.primary:hover { background: var(--primary); opacity: 0.9; }

        /* İstatistik Kartları */
        .grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem; }
        .card { background: var(--sidebar-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .card span { font-size: 0.875rem; font-weight: 500; color: var(--muted); text-transform: capitalize; display: block; margin-bottom: 0.5rem; }
        .card strong { font-size: 1.5rem; font-weight: 700; color: var(--foreground); display: block; }

        /* Tablo Paneli */
        .panel { background: var(--sidebar-bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); overflow: hidden; }
        .panel .top { padding: 1.5rem; border-bottom: 1px solid var(--border); margin-bottom: 0; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem; }
        th { font-weight: 500; color: var(--muted-foreground); padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); color: var(--foreground); vertical-align: middle; }
        tbody tr:hover { background: var(--accent); }
        tbody tr:last-child td { border-bottom: none; }
        td a { font-weight: 500; color: var(--foreground); text-decoration: underline; text-underline-offset: 4px; decoration-color: var(--border); }
        td a:hover { color: var(--primary); }
        td small { color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 2px; }

        /* Durum Rozeti (Badge) */
        .status-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; background: var(--accent); color: var(--foreground); border: 1px solid var(--border); }
    </style>

    <div class="top">
        <div>
            <p class="eyebrow">Yönetim paneli</p>
            <h1>Dashboard</h1>
        </div>
        <a class="btn primary" href="{{ route('admin.products.create') }}">Yeni Ürün</a>
    </div>

    <section class="grid" aria-label="Özet">
        @foreach($stats as $label => $value)
            <article class="card">
                <span>{{ str_replace('_', ' ', $label) }}</span>
                <strong>{{ $value }}</strong>
            </article>
        @endforeach
    </section>

    <section class="panel">
        <div class="top">
            <div>
                <p class="eyebrow">Son hareketler</p>
                <h1>Son siparişler</h1>
            </div>
            <a class="btn" href="{{ route('admin.orders.index') }}">Tümünü Gör</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Sipariş</th>
                    <th>Müşteri</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Ödeme</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->merchant_oid }}</a></td>
                        <td>{{ $order->customer_name }}<br><small>{{ $order->customer_email }}</small></td>
                        <td>{{ number_format($order->total_cents / 100, 2, ',', '.') }} {{ $order->currency }}</td>
                        <td><span class="status-badge">{{ $order->status->value }}</span></td>
                        <td><span class="status-badge">{{ $order->payment?->status->value ?? '-' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">Sipariş yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection