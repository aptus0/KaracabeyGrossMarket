<x-layouts.admin header="Müşteriler & Yetkililer">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Kullanıcılar</h2>
                <p class="text-muted-foreground">Sistemdeki yöneticileri ve müşterileri yönetin.</p>
            </div>
            <x-ui.button as="a" href="#" variant="outline">
                <x-lucide-download class="mr-2 h-4 w-4" /> CSV İndir
            </x-ui.button>
        </div>

        <x-ui.card>
            <div class="p-6 pb-0 border-b pb-6 flex items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                    <x-ui.input type="search" name="q" placeholder="İsim veya e-posta ile arayın..." class="pl-9 bg-muted/50" />
                </div>
            </div>
            
            <x-ui.table>
                <x-slot name="header">
                    <tr>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">İsim</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">E-posta</th>
                        <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Siparişler</th>
                        <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Rol</th>
                        <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Kayıt Tarihi</th>
                    </tr>
                </x-slot>

                @forelse($users as $user)
                    <tr class="border-b transition-colors hover:bg-muted/30">
                        <td class="p-4 px-6 align-middle font-medium">{{ $user->name }}</td>
                        <td class="p-4 px-6 align-middle">{{ $user->email }}</td>
                        <td class="p-4 px-6 align-middle text-center">{{ $user->orders_count ?? 0 }}</td>
                        <td class="p-4 px-6 align-middle">
                            @if($user->is_admin)
                                <x-ui.badge variant="default" class="bg-primary/20 text-primary hover:bg-primary/30">Yönetici</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Müşteri</x-ui.badge>
                            @endif
                        </td>
                        <td class="p-4 px-6 align-middle text-right text-sm text-muted-foreground">
                            {{ $user->created_at?->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-muted-foreground">Kullanıcı bulunamadı.</td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if($users->hasPages())
                <div class="p-4 px-6 border-t bg-muted/20">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
