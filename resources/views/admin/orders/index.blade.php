@extends('admin.layout')

@section('title', 'Siparisler')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Operasyon</p>
            <h1>Siparisler</h1>
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Siparis</th>
                    <th>Musteri</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->merchant_oid }}</td>
                        <td>{{ $order->customer_name }}<br><small>{{ $order->customer_email }}</small></td>
                        <td>{{ number_format($order->total_cents / 100, 2, ',', '.') }} {{ $order->currency }}</td>
                        <td>{{ $order->status->value }}</td>
                        <td>{{ $order->created_at?->format('d.m.Y H:i') }}</td>
                        <td><a class="btn" href="{{ route('admin.orders.show', $order) }}">Detay</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Siparis yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $orders->links() }}</div>
    </section>
@endsection
