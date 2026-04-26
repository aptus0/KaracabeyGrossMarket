@extends('admin.layout')

@section('title', 'Kategoriler')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Katalog</p>
            <h1>Kategoriler</h1>
        </div>
    </div>

    <section class="panel">
        <form class="form" action="{{ route('admin.categories.store') }}" method="post">
            @csrf
            <label>Ad
                <input name="name" required>
            </label>
            <label>Slug
                <input name="slug">
            </label>
            <label>Sira
                <input name="sort_order" type="number" min="0" value="0">
            </label>
            <label class="check-row">
                <input name="is_active" type="checkbox" value="1" checked>
                Aktif
            </label>
            <button class="btn primary" type="submit">Kategori Ekle</button>
        </form>
    </section>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Slug</th>
                    <th>Sira</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->sort_order }}</td>
                        <td>{{ $category->is_active ? 'Aktif' : 'Pasif' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Kategori yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $categories->links() }}</div>
    </section>
@endsection
