<x-layouts.admin header="Pazarlama & SEO">
    <div class="flex flex-col gap-6 max-w-4xl mx-auto w-full">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Google & Meta Ayarları</h2>
                <p class="text-muted-foreground">İzleme piksellerini ve reklam entegrasyonlarını yönetin.</p>
            </div>
        </div>

        <form action="{{ route('admin.marketing.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-6">
                <!-- Google Integrations -->
                <x-ui.card>
                    <div class="p-6 border-b flex flex-col space-y-1.5">
                        <div class="flex items-center gap-2">
                            <x-lucide-bar-chart class="h-5 w-5 text-primary" />
                            <h3 class="font-semibold tracking-tight">Google Entegrasyonu</h3>
                        </div>
                        <p class="text-sm text-muted-foreground">Google Analytics, Ads ve Search Console doğrulamasını yapılandırın.</p>
                    </div>
                    <div class="p-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <x-ui.label for="google_analytics_id">Google Analytics Kimliği (ID)</x-ui.label>
                            <x-ui.input id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $setting->google_analytics_id) }}" placeholder="G-XXXXXXXXXX" />
                            <p class="text-[0.8rem] text-muted-foreground">GA4 izlemesi için kullanılır.</p>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="google_site_verification">Search Console Doğrulaması</x-ui.label>
                            <x-ui.input id="google_site_verification" name="google_site_verification" value="{{ old('google_site_verification', $setting->google_site_verification) }}" placeholder="html tag içeriği..." />
                        </div>
                        <div class="space-y-2 border-t pt-6 md:col-span-2">
                            <h4 class="text-sm font-medium mb-4">Google Ads</h4>
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="google_ads_id">Google Ads Kimliği (ID)</x-ui.label>
                            <x-ui.input id="google_ads_id" name="google_ads_id" value="{{ old('google_ads_id', $setting->google_ads_id) }}" placeholder="AW-XXXXXXXXXX" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="google_ads_conversion_label">Dönüşüm Etiketi (Conversion Label)</x-ui.label>
                            <x-ui.input id="google_ads_conversion_label" name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $setting->google_ads_conversion_label) }}" />
                        </div>
                    </div>
                </x-ui.card>

                <!-- Meta Integrations -->
                <x-ui.card>
                    <div class="p-6 border-b flex flex-col space-y-1.5">
                        <div class="flex items-center gap-2">
                            <x-lucide-facebook class="h-5 w-5 text-blue-600" />
                            <h3 class="font-semibold tracking-tight">Meta (Facebook) Entegrasyonu</h3>
                        </div>
                        <p class="text-sm text-muted-foreground">Reklam izlemesi için Meta Pixel yapılandırın.</p>
                    </div>
                    <div class="p-6 grid gap-6">
                        <div class="space-y-2 max-w-md">
                            <x-ui.label for="meta_pixel_id">Meta Pixel ID</x-ui.label>
                            <x-ui.input id="meta_pixel_id" name="meta_pixel_id" value="{{ old('meta_pixel_id', $setting->meta_pixel_id) }}" placeholder="Örn: 123456789012345" />
                        </div>
                    </div>
                </x-ui.card>

                <!-- Actions -->
                <div class="flex items-center justify-end border-t pt-6 pb-12">
                    <x-ui.button type="submit">
                        <x-lucide-save class="mr-2 h-4 w-4" /> Ayarları Kaydet
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
