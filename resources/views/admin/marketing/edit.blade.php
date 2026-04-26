@extends('admin.layout')

@section('title', 'SEO / Pixel')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">SEO / Marketing</p>
            <h1>Google ve Meta Ayarlari</h1>
        </div>
    </div>

    <section class="panel">
        <form class="form" action="{{ route('admin.marketing.update') }}" method="post">
            @csrf
            @method('put')

            <label>Google Analytics ID
                <input name="google_analytics_id" value="{{ old('google_analytics_id', $setting->google_analytics_id) }}" placeholder="G-XXXXXXXXXX">
            </label>
            <label>Google Ads ID
                <input name="google_ads_id" value="{{ old('google_ads_id', $setting->google_ads_id) }}" placeholder="AW-XXXXXXXXXX">
            </label>
            <label>Google Ads Conversion Label
                <input name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $setting->google_ads_conversion_label) }}">
            </label>
            <label>Search Console Verification
                <input name="google_site_verification" value="{{ old('google_site_verification', $setting->google_site_verification) }}">
            </label>
            <label>Meta Pixel ID
                <input name="meta_pixel_id" value="{{ old('meta_pixel_id', $setting->meta_pixel_id) }}">
            </label>
            <button class="btn primary" type="submit">Kaydet</button>
        </form>
    </section>
@endsection
