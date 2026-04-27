<aside class="w-64 border-r bg-card shrink-0 transition-transform md:translate-x-0" 
    :class="sidebarOpen ? 'translate-x-0 fixed inset-y-0 left-0 z-50 shadow-xl' : '-translate-x-full fixed inset-y-0 left-0 z-50 md:static md:block'">
    
    <div class="h-16 flex items-center px-6 border-b bg-card">
        <a href="/admin/products" class="flex items-center gap-2 font-bold text-xl tracking-tight text-primary">
            <x-lucide-store class="h-6 w-6" />
            Karacabey Gross
        </a>
    </div>

    <div class="p-4 space-y-4">
        <nav class="space-y-1.5">
            <a href="/admin/products" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ request()->is('admin/products*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <x-lucide-package class="h-4 w-4" />
                Products
            </a>
            <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                <x-lucide-users class="h-4 w-4" />
                Customers
            </a>
            <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                <x-lucide-shopping-cart class="h-4 w-4" />
                Orders
            </a>
            <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                <x-lucide-settings class="h-4 w-4" />
                Settings
            </a>
        </nav>
    </div>
</aside>
<!-- Mobile Overlay -->
<div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-background/80 backdrop-blur-sm md:hidden"></div>
