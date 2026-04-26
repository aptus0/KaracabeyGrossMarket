@extends('admin.layout')

@section('title', 'Ana Sayfa')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Page Builder</p>
            <h1>Ana Sayfa Bloklari</h1>
        </div>
    </div>

    <section class="panel">
        <form class="form" action="{{ route('admin.homepage-blocks.store') }}" method="post">
            @csrf
            <label>Tip
                <select name="type">
                    <option value="hero">Hero</option>
                    <option value="campaign">Kampanya</option>
                    <option value="content">Icerik</option>
                    <option value="product_slider">Urun Slider</option>
                </select>
            </label>
            <label>Baslik
                <input name="title" required>
            </label>
            <label>Alt metin
                <textarea name="subtitle"></textarea>
            </label>
            <label>Gorsel URL
                <input name="image_url" type="url">
            </label>
            <label>Link URL
                <input name="link_url">
            </label>
            <label>Link etiketi
                <input name="link_label">
            </label>
            <label>Sira
                <input name="sort_order" type="number" min="0" value="0">
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" checked>
                Aktif
            </label>
            <button class="btn primary" type="submit">Blok Ekle</button>
        </form>
    </section>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Baslik</th>
                    <th>Tip</th>
                    <th>Link</th>
                    <th>Sira</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blocks as $block)
                    <tr>
                        <td>{{ $block->title }}<br><small>{{ $block->subtitle }}</small></td>
                        <td>{{ $block->type }}</td>
                        <td>{{ $block->link_label ?: '-' }}<br><small>{{ $block->link_url }}</small></td>
                        <td>{{ $block->sort_order }}</td>
                        <td>{{ $block->is_active ? 'Aktif' : 'Pasif' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Blok yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $blocks->links() }}</div>
    </section>
@endsection
