@props([
    'title' => 'Menu',
])

<div x-data="{ mobileMenuOpen: false }" class="md:hidden">
    <!-- Przycisk otwierający -->
    <button 
        @click="mobileMenuOpen = true" 
        type="button" 
        class="fixed top-4 right-4 z-50 bg-white dark:bg-gray-800 p-2 rounded-md shadow-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
        aria-controls="mobile-menu" 
        aria-expanded="false"
    >
        <span class="sr-only">Otwórz menu</span>
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Panel mobilny -->
    <div 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black bg-opacity-75"
        x-cloak
        @click="mobileMenuOpen = false"
    ></div>

    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-white dark:bg-gray-800 overflow-y-auto"
        x-cloak
    >
        <div class="px-4 pt-5 pb-2 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h2>
            <button 
                @click="mobileMenuOpen = false" 
                type="button" 
                class="rounded-md p-2 text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 dark:focus:ring-gray-600"
            >
                <span class="sr-only">Zamknij menu</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="py-4 px-2">
            {{ $slot }}
        </div>
    </div>
</div> 