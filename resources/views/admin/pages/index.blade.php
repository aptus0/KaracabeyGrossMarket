@extends('admin.layout')

@section('title', 'Sayfalar')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Icerik</p>
            <h1>Sayfalar</h1>
        </div>
        <a class="btn primary" href="{{ route('admin.pages.create') }}">Yeni Sayfa</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Baslik</th>
                    <th>Slug</th>
                    <th>Grup</th>
                    <th>SEO</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>{{ $page->title }}</td>
                        <td>{{ $page->slug }}</td>
                        <td>{{ $page->group }}</td>
                        <td>{{ $page->seo_title ?: '-' }}<br><small>{{ $page->seo_description }}</small></td>
                        <td>{{ $page->is_published ? 'Yayinda' : 'Taslak' }}</td>
                        <td><a class="btn" href="{{ route('admin.pages.edit', $page) }}">Duzenle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Sayfa yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $pages->links() }}</div>
    </section>
@endsection
