@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Yonetim paneli</p>
            <h1>Dashboard</h1>
        </div>
        <a class="btn primary" href="{{ route('admin.products.create') }}">Yeni Urun</a>
    </div>

    <section class="grid" aria-label="Ozet">
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
                <h1>Son siparisler</h1>
            </div>
            <a class="btn" href="{{ route('admin.orders.index') }}">Tumunu Gor</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Siparis</th>
                    <th>Musteri</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Odeme</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->merchant_oid }}</a></td>
                        <td>{{ $order->customer_name }}<br><small>{{ $order->customer_email }}</small></td>
                        <td>{{ number_format($order->total_cents / 100, 2, ',', '.') }} {{ $order->currency }}</td>
                        <td>{{ $order->status->value }}</td>
                        <td>{{ $order->payment?->status->value ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Siparis yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
