@extends('admin.layout')

@section('title', 'Navigasyon')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Header / Footer</p>
            <h1>Navigasyon Menuleri</h1>
        </div>
    </div>

    <section class="panel">
        <form class="form" action="{{ route('admin.navigation.store') }}" method="post">
            @csrf
            <label>Konum
                <select name="placement" required>
                    @foreach($placements as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Baslik
                <input name="label" value="{{ old('label') }}" maxlength="80" required>
            </label>
            <label>URL
                <input name="url" value="{{ old('url') }}" placeholder="/products" maxlength="500" required>
            </label>
            <label>Ikon
                <select name="icon">
                    <option value="">Ikonsuz</option>
                    @foreach($icons as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Sira
                <input name="sort_order" type="number" min="0" max="10000" value="{{ old('sort_order', 0) }}">
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" checked>
                Aktif
            </label>
            <button class="btn primary" type="submit">Menu Ogesi Ekle</button>
        </form>
    </section>

    <section class="panel">
        @foreach($items as $item)
            <form id="nav-{{ $item->id }}" action="{{ route('admin.navigation.update', $item) }}" method="post">
                @csrf
                @method('put')
            </form>
        @endforeach

        <table>
            <thead>
                <tr>
                    <th>Konum</th>
                    <th>Baslik</th>
                    <th>URL</th>
                    <th>Ikon</th>
                    <th>Sira</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <select name="placement" form="nav-{{ $item->id }}">
                                @foreach($placements as $value => $label)
                                    <option value="{{ $value }}" @selected($item->placement === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input name="label" form="nav-{{ $item->id }}" value="{{ $item->label }}" maxlength="80" required></td>
                        <td><input name="url" form="nav-{{ $item->id }}" value="{{ $item->url }}" maxlength="500" required></td>
                        <td>
                            <select name="icon" form="nav-{{ $item->id }}">
                                <option value="">Ikonsuz</option>
                                @foreach($icons as $value => $label)
                                    <option value="{{ $value }}" @selected($item->icon === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input name="sort_order" form="nav-{{ $item->id }}" type="number" min="0" max="10000" value="{{ $item->sort_order }}"></td>
                        <td>
                            <label class="check-row">
                                <input name="is_active" form="nav-{{ $item->id }}" type="checkbox" value="1" @checked($item->is_active)>
                                Aktif
                            </label>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn primary" form="nav-{{ $item->id }}" type="submit">Kaydet</button>
                                <form action="{{ route('admin.navigation.destroy', $item) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn danger" type="submit">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Menu ogesi yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $items->links() }}</div>
    </section>
@endsection
