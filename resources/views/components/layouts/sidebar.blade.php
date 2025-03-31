@props([
    'menuType' => 'user' // Parametr menuType nie jest już potrzebny, ale zostawiamy dla kompatybilności
])

<!-- Główny komponent sidebar -->
<aside 
    class="fixed inset-y-0 left-0 z-40 transition-all duration-300 bg-steel-blue-800/95 border-r border-steel-blue-700/40 shadow-lg"
    :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}"
    x-cloak
>
    <!-- Kontener dla całego sidebara -->
    <div class="flex flex-col h-full">
        <!-- Header sidebara z logo -->
        <div class="flex h-16 items-center justify-between px-3 border-b border-steel-blue-700/40">
            <!-- Logo i nazwa -->
            <a href="{{ auth()->user()->can('admin') && request()->segment(1) === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center">
                <div class="w-8 h-8 rounded-full overflow-hidden bg-white/20 shadow-lg flex items-center justify-center">
                    <img src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-6 w-auto object-contain" />
                </div>
                <span 
                    x-show="sidebarOpen" 
                    x-transition:enter="transition-opacity ease-in-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in-out duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-2 text-white font-bold text-lg"
                >{{ config('app.name') }}</span>
            </a>
            
            <!-- Przycisk do zwijania menu -->
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="h-8 w-8 flex items-center justify-center text-white/80 hover:text-white rounded-full hover:bg-steel-blue-700/50 transition-colors"
                aria-label="{{ __('Zwiń/rozwiń menu') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-300" :class="{'rotate-180': !sidebarOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
        
        <!-- Kontener menu -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <x-menu.app-menu />
        </div>
        
        <!-- Stopka sidebara -->
        <div class="p-3 text-xs text-white/60 border-t border-steel-blue-700/40" x-show="sidebarOpen">
            <div>
                <div>{{ config('app.name') }} v{{ config('app.version', '1.0.0') }}</div>
                <div class="mt-1">{{ now()->format('d.m.Y') }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Overlay do zacienienia tła na urządzeniach mobilnych gdy menu jest otwarte -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden" 
    x-cloak
></div>

<!-- Przycisk otwarcia menu na urządzeniach mobilnych -->
<button 
    @click="sidebarOpen = true" 
    class="fixed bottom-4 right-4 z-30 bg-steel-blue-700 text-white p-3 rounded-full shadow-lg lg:hidden hover:bg-steel-blue-800 transition-colors"
    x-show="!sidebarOpen"
    x-cloak
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button> 