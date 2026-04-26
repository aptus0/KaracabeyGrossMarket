@extends('admin.layout')

@section('title', 'Siparis Detay')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Siparis</p>
            <h1>{{ $order->merchant_oid }}</h1>
        </div>
        <a class="btn" href="{{ route('admin.orders.index') }}">Listeye Don</a>
    </div>

    <section class="grid">
        <article class="card"><span>Durum</span><strong>{{ $order->status->value }}</strong></article>
        <article class="card"><span>Odeme</span><strong>{{ $order->payment?->status->value ?? '-' }}</strong></article>
        <article class="card"><span>Tutar</span><strong>{{ number_format($order->total_cents / 100, 2, ',', '.') }}</strong></article>
        <article class="card"><span>Musteri</span><strong>{{ $order->customer_name }}</strong></article>
        <article class="card"><span>Telefon</span><strong>{{ $order->customer_phone }}</strong></article>
    </section>

    <section class="panel">
        <p><strong>Adres:</strong> {{ $order->shipping_city }} {{ $order->shipping_district }} {{ $order->shipping_address }}</p>
        <table>
            <thead>
                <tr>
                    <th>Urun</th>
                    <th>Adet</th>
                    <th>Birim</th>
                    <th>Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price_cents / 100, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->line_total_cents / 100, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
