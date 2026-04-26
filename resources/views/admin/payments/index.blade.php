@extends('admin.layout')

@section('title', 'Odemeler')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">PayTR</p>
            <h1>Odemeler</h1>
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Merchant OID</th>
                    <th>Siparis</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Iade</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->merchant_oid }}</td>
                        <td>{{ $payment->order?->customer_name ?? '-' }}</td>
                        <td>{{ number_format($payment->amount_cents / 100, 2, ',', '.') }} {{ $payment->currency }}</td>
                        <td>{{ $payment->status->value }}</td>
                        <td>{{ number_format($payment->refunds->where('status', 'success')->sum('amount_cents') / 100, 2, ',', '.') }}</td>
                        <td>{{ $payment->created_at?->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Odeme yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $payments->links() }}</div>
    </section>
@endsection
