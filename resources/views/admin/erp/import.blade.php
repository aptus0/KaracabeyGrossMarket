<x-layouts.admin header="ERP Ürün Aktarımı">
    <div class="flex flex-col gap-6 max-w-4xl mx-auto w-full" x-data="erpImport()">

        {{-- Sayfa Başlığı --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">ERP Ürün Aktarımı</h2>
                <p class="text-sm text-slate-500">Erkur SQL Server'dan ürünleri canlı olarak çekin</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                pdo_sqlsrv aktif
            </span>
        </div>

        {{-- Son İmport Bilgisi --}}
        @if($lastImport)
        <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            Son aktarım: <strong>{{ $lastImport['date'] }}</strong> —
            <strong>{{ number_format($lastImport['imported']) }}</strong> ürün
            ({{ $lastImport['host'] }})
        </div>
        @endif

        {{-- Bağlantı Formu --}}
        <x-ui.card class="rounded-lg">
            <div class="border-b px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-800">SQL Server Bağlantı Bilgileri</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <x-ui.label for="erp-host">Sunucu Adresi *</x-ui.label>
                        <x-ui.input id="erp-host" x-model="form.host" placeholder="192.168.1.100 veya SUNUCU\SQLEXPRESS" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="erp-port">Port</x-ui.label>
                        <x-ui.input id="erp-port" x-model="form.port" placeholder="1433" type="number" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="erp-db">Veritabanı Adı *</x-ui.label>
                        <x-ui.input id="erp-db" x-model="form.database" placeholder="ERKUR_DB" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="erp-user">Kullanıcı Adı *</x-ui.label>
                        <x-ui.input id="erp-user" x-model="form.username" placeholder="sa" />
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <x-ui.label for="erp-pass">Şifre</x-ui.label>
                        <x-ui.input id="erp-pass" x-model="form.password" type="password" placeholder="••••••••" />
                    </div>
                </div>

                {{-- Test Sonucu --}}
                <div x-show="testResult" x-cloak>
                    <div x-show="testResult && testResult.success"
                         class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        ✓ <span x-text="testResult ? testResult.message : ''"></span>
                    </div>
                    <div x-show="testResult && !testResult.success"
                         class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        ✗ <span x-text="testResult ? testResult.message : ''"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-ui.button
                        type="button"
                        variant="outline"
                        class="rounded-md"
                        @click="testConnection()"
                        :disabled="testing || importing">
                        <span x-show="!testing">🔌 Bağlantıyı Test Et</span>
                        <span x-show="testing">Test ediliyor...</span>
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>

        {{-- İmport Seçenekleri --}}
        <x-ui.card class="rounded-lg">
            <div class="border-b px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-800">Aktarım Seçenekleri</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 rounded-md border border-amber-100 bg-amber-50 p-4">
                    <x-ui.checkbox id="fresh-import" x-model="form.fresh" value="1" />
                    <div>
                        <x-ui.label for="fresh-import" class="cursor-pointer font-medium">Mevcut ürünleri sil ve sıfırdan aktar</x-ui.label>
                        <p class="text-xs text-slate-500 mt-0.5">İşaretlenmezse mevcut ürünlerin üzerine yazılır (slug ile eşleştirme yapılmaz).</p>
                    </div>
                </div>

                {{-- Progress --}}
                <div x-show="importing" x-cloak class="space-y-2">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span x-text="progress.step">Hazırlanıyor...</span>
                        <span x-text="progress.pct + '%'">0%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-orange-500 transition-all duration-500"
                             :style="'width:' + progress.pct + '%'"></div>
                    </div>
                </div>

                {{-- Sonuç --}}
                <div x-show="importResult" x-cloak>
                    <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        ✓ <span x-text="importResult ? importResult.message : ''"></span>
                    </div>
                </div>
                <div x-show="importError" x-cloak>
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        ✗ <span x-text="importError"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-ui.button
                        type="button"
                        class="rounded-md"
                        @click="startImport()"
                        :disabled="importing || !(testResult && testResult.success)">
                        <span x-show="!importing">🚀 Aktarımı Başlat</span>
                        <span x-show="importing">Aktarılıyor...</span>
                    </x-ui.button>
                    <p class="text-xs text-slate-400">Önce bağlantıyı test edin.</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Geçmiş --}}
        @if(count($history) > 0)
        <x-ui.card class="rounded-lg">
            <div class="border-b px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-800">Son Aktarımlar</h3>
            </div>
            <div class="divide-y">
                @foreach($history as $h)
                <div class="flex items-center justify-between px-6 py-3 text-sm">
                    <div>
                        <span class="font-medium text-slate-700">{{ $h['date'] }}</span>
                        <span class="ml-2 text-slate-400 text-xs">{{ $h['host'] }}</span>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                        {{ number_format($h['imported']) }} ürün
                    </span>
                </div>
                @endforeach
            </div>
        </x-ui.card>
        @endif
    </div>

    @push('scripts')
    <script>
    function erpImport() {
        return {
            form: { host: '', port: 1433, database: '', username: '', password: '', fresh: false },
            testing: false,
            importing: false,
            testResult: null,
            importResult: null,
            importError: null,
            progress: { step: 'Başlatılıyor...', pct: 0 },
            pollTimer: null,

            async testConnection() {
                this.testing = true;
                this.testResult = null;
                try {
                    const res = await fetch('{{ route('admin.erp.test-connection') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.form),
                    });
                    this.testResult = await res.json();
                } catch(e) {
                    this.testResult = { success: false, message: 'Ağ hatası: ' + e.message };
                }
                this.testing = false;
            },

            async startImport() {
                this.importing = true;
                this.importResult = null;
                this.importError = null;
                this.progress = { step: 'Başlatılıyor...', pct: 0 };
                this.pollTimer = setInterval(() => this.pollStatus(), 1500);

                try {
                    const res = await fetch('{{ route('admin.erp.import.run') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.importResult = data;
                        this.progress = { step: 'Tamamlandı!', pct: 100 };
                    } else {
                        this.importError = data.message;
                    }
                } catch(e) {
                    this.importError = 'Hata: ' + e.message;
                }
                clearInterval(this.pollTimer);
                this.importing = false;
            },

            async pollStatus() {
                try {
                    const res = await fetch('{{ route('admin.erp.import.status') }}');
                    const data = await res.json();
                    if (data.progress) this.progress = data.progress;
                } catch(_) {}
            }
        }
    }
    </script>
    @endpush
</x-layouts.admin>
