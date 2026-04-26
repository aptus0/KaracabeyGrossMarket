@extends('admin.layout')

@section('title', 'Kampanyalar')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Marketing</p>
            <h1>Kampanya / Kupon</h1>
        </div>
    </div>

    <section class="panel">
        <h2>Yeni Kampanya</h2>
        <form class="form" action="{{ route('admin.campaigns.store') }}" method="post">
            @csrf
            <label>Ad
                <input name="name" required>
            </label>
            <label>Slug
                <input name="slug">
            </label>
            <label>Aciklama
                <textarea name="description"></textarea>
            </label>
            <label>Banner gorsel URL
                <input name="banner_image_url" type="url">
            </label>
            <label>Indirim tipi
                <select name="discount_type">
                    <option value="fixed">Sabit</option>
                    <option value="percent">Yuzde</option>
                </select>
            </label>
            <label>Indirim degeri
                <input name="discount_value" type="number" min="0" value="0" required>
            </label>
            <label>SEO basligi
                <input name="seo_title">
            </label>
            <label>SEO aciklamasi
                <textarea name="seo_description"></textarea>
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" checked>
                Aktif
            </label>
            <button class="btn primary" type="submit">Kampanya Ekle</button>
        </form>
    </section>

    <section class="panel">
        <h2>Yeni Kupon</h2>
        <form class="form" action="{{ route('admin.coupons.store') }}" method="post">
            @csrf
            <label>Kampanya
                <select name="campaign_id">
                    <option value="">Bagimsiz kupon</option>
                    @foreach($campaigns as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Kod
                <input name="code" required>
            </label>
            <label>Indirim tipi
                <select name="discount_type">
                    <option value="fixed">Sabit</option>
                    <option value="percent">Yuzde</option>
                </select>
            </label>
            <label>Indirim degeri
                <input name="discount_value" type="number" min="0" value="0" required>
            </label>
            <label>Minimum sepet kurus
                <input name="minimum_order_cents" type="number" min="0" value="0">
            </label>
            <label>Kullanim limiti
                <input name="usage_limit" type="number" min="1">
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" checked>
                Aktif
            </label>
            <button class="btn primary" type="submit">Kupon Ekle</button>
        </form>
    </section>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Kampanya</th>
                    <th>Indirim</th>
                    <th>Kupon</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->name }}<br><small>{{ $campaign->slug }}</small></td>
                        <td>{{ $campaign->discount_type }} / {{ $campaign->discount_value }}</td>
                        <td>{{ $campaign->coupons->pluck('code')->join(', ') ?: '-' }}</td>
                        <td>{{ $campaign->is_active ? 'Aktif' : 'Pasif' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Kampanya yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $campaigns->links() }}</div>
    </section>
@endsection
