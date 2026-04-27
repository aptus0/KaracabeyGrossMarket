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
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
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
        @foreach($blocks as $block)
            <form id="block-{{ $block->id }}" action="{{ route('admin.homepage-blocks.update', $block) }}" method="post">
                @csrf
                @method('put')
            </form>
        @endforeach

        <table>
            <thead>
                <tr>
                    <th>Baslik</th>
                    <th>Tip</th>
                    <th>Link</th>
                    <th>Sira</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($blocks as $block)
                    <tr>
                        <td>
                            <input name="title" form="block-{{ $block->id }}" value="{{ $block->title }}" required>
                            <textarea name="subtitle" form="block-{{ $block->id }}" placeholder="Alt metin">{{ $block->subtitle }}</textarea>
                            <input name="image_url" form="block-{{ $block->id }}" value="{{ $block->image_url }}" placeholder="Gorsel URL">
                        </td>
                        <td>
                            <select name="type" form="block-{{ $block->id }}">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected($block->type === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input name="link_label" form="block-{{ $block->id }}" value="{{ $block->link_label }}" placeholder="Link etiketi">
                            <input name="link_url" form="block-{{ $block->id }}" value="{{ $block->link_url }}" placeholder="/products">
                        </td>
                        <td><input name="sort_order" form="block-{{ $block->id }}" type="number" min="0" max="10000" value="{{ $block->sort_order }}"></td>
                        <td>
                            <label class="check-row">
                                <input name="is_active" form="block-{{ $block->id }}" type="checkbox" value="1" @checked($block->is_active)>
                                Aktif
                            </label>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn primary" form="block-{{ $block->id }}" type="submit">Kaydet</button>
                                <form action="{{ route('admin.homepage-blocks.destroy', $block) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn danger" type="submit">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Blok yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $blocks->links() }}</div>
    </section>
@endsection
