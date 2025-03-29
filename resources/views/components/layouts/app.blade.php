@props([
    'header' => null,
])

<x-layouts.base>
    <div class="min-h-screen" x-data="{ sidebarOpen: $persist(true).as('sidebar_state') }" data-section="dashboard">
        <!-- Sidebar -->
        <x-layouts.sidebar menuType="user">
        </x-layouts.sidebar>
        
        <!-- Main Content -->
        <div class="transition-all duration-300" :class="{'pl-64': sidebarOpen, 'pl-16': !sidebarOpen}">
            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-800 shadow border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $header ?? 'Panel użytkownika' }}
                    </h2>
                    
                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 dark:text-gray-300 focus:outline-none">
                            <div class="w-9 h-9 bg-indigo-700 dark:bg-gray-600 rounded-full flex items-center justify-center text-white font-semibold mr-2 shadow-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 py-2 bg-white dark:bg-gray-800 rounded-md shadow-xl z-20 border border-gray-200 dark:border-gray-700"
                             x-cloak>
                             
                             <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                 <i class="far fa-user-circle mr-2"></i> Profil
                             </a>
                             
                             @if(auth()->user()->hasRole('admin'))
                             <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                 <i class="fas fa-user-shield mr-2"></i> Panel Admina
                             </a>
                             @endif
                             
                             <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                             
                             <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Wyloguj
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="py-6 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Komunikaty systemowe -->
                    @if (session('success'))
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-400 text-emerald-700 dark:text-emerald-300 p-3 mb-4 rounded shadow-sm" role="alert">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 text-red-700 dark:text-red-300 p-3 mb-4 rounded shadow-sm" role="alert">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 text-red-700 dark:text-red-300 p-3 mb-4 rounded shadow-sm" role="alert">
                            <p class="text-sm font-medium">Proszę poprawić poniższe błędy:</p>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    {{ $slot }}
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    &copy; {{ date('Y') }} {{ config('app.name') }} - Wszelkie prawa zastrzeżone
                </div>
            </footer>
        </div>
    </div>
</x-layouts.base> 