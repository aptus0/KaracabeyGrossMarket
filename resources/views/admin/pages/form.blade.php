@extends('admin.layout')

@section('title', $page->exists ? 'Sayfa Duzenle' : 'Yeni Sayfa')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Icerik</p>
            <h1>{{ $page->exists ? 'Sayfa Duzenle' : 'Yeni Sayfa' }}</h1>
        </div>
        <a class="btn" href="{{ route('admin.pages.index') }}">Listeye Don</a>
    </div>

    <section class="panel">
        <form class="form" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="post">
            @csrf
            @if($page->exists)
                @method('put')
            @endif

            <label>Baslik
                <input name="title" value="{{ old('title', $page->title) }}" required>
            </label>
            <label>Slug
                <input name="slug" value="{{ old('slug', $page->slug) }}">
            </label>
            <label>Grup
                <select name="group">
                    @foreach(['corporate' => 'Kurumsal', 'legal' => 'Yasal', 'support' => 'Destek'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('group', $page->group ?: 'corporate') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Icerik
                <textarea name="body">{{ old('body', $page->body) }}</textarea>
            </label>
            <label>SEO basligi
                <input name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
            </label>
            <label>SEO aciklamasi
                <textarea name="seo_description">{{ old('seo_description', $page->seo_description) }}</textarea>
            </label>
            <label>Meta gorsel URL
                <input name="meta_image_url" type="url" value="{{ old('meta_image_url', $page->meta_image_url) }}">
            </label>
            <label class="check-row">
                <input name="is_published" type="checkbox" value="1" @checked(old('is_published', $page->is_published ?? true))>
                Yayinda
            </label>
            <button class="btn primary" type="submit">Kaydet</button>
        </form>
    </section>
@endsection
