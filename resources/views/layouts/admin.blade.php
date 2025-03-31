<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-cache-control" content="no-preview">

    <title>{{ config('app.name', 'Laravel') }} - Panel administratora</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles -->
    @livewireStyles
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.1/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Inicjalizacja motywu ciemnego po załadowaniu Alpine -->
    <script>
        document.addEventListener('alpine:init', () => {
            if (window.Alpine && !Alpine.store('theme')) {
                // Tymczasowe zabezpieczenie w przypadku braku magazynu theme
                Alpine.store('theme', {
                    isDark: localStorage.getItem('dark_mode') === 'true',
                    userChosen: localStorage.getItem('user_chosen_theme') === 'true',
                    toggle() {
                        this.isDark = !this.isDark;
                        this.userChosen = true;
                        localStorage.setItem('dark_mode', this.isDark);
                        localStorage.setItem('user_chosen_theme', 'true');
                        this.updateTheme();
                    },
                    updateTheme() {
                        if (this.isDark) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    },
                    resetToSystemPreference() {
                        this.userChosen = false;
                        localStorage.removeItem('user_chosen_theme');
                        this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        localStorage.setItem('dark_mode', this.isDark);
                        this.updateTheme();
                    },
                    init() {
                        this.updateTheme();
                    }
                });
                console.log('Dodano awaryjny magazyn theme w alpine:init (admin)');
            }
        });
    </script>

    <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside class="bg-steel-blue-800 text-white w-64 flex flex-col fixed inset-y-0 h-screen z-20 transition-all duration-300"
               :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}">
            
            <!-- Logo i nazwa aplikacji -->
            <div class="p-4 flex items-center justify-between border-b border-steel-blue-700/40">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                    <img src="/images/logo.svg" alt="Logo" class="h-8 w-auto">
                    <span class="ml-3 text-lg font-semibold text-white" x-show="sidebarOpen">{{ config('app.name') }}</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white/70 hover:text-white focus:outline-none">
                    <i class="fas fa-bars" x-show="sidebarOpen"></i>
                    <i class="fas fa-chevron-right" x-show="!sidebarOpen"></i>
                </button>
            </div>
            
            <!-- Nawigacja -->
            <div class="py-4 flex-1 h-full overflow-y-auto">
                <x-menu.app-menu />
            </div>
        </aside>

        <!-- Główna zawartość -->
        <main class="flex-1 transition-all duration-300" :class="{'ml-64': sidebarOpen, 'ml-16': !sidebarOpen}">
            <!-- Górny pasek nawigacyjny -->
            <nav class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-4 py-3 flex justify-between items-center">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="ml-4 text-lg font-medium text-gray-800">Panel administratora</h1>
                    </div>
                    
                    <div class="flex items-center">
                        <!-- Powiadomienia -->
                        <div class="relative mr-4">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bell"></i>
                            </button>
                            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                        </div>
                        
                        <!-- Profil użytkownika -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center focus:outline-none">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7F9CF5&background=EBF4FF" alt="Avatar" class="h-8 w-8 rounded-full">
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profil
                                </a>
                                
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-arrow-left mr-2"></i> Powrót do aplikacji
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Wyloguj się
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Zawartość strony -->
            <div class="py-6">
                @yield('content')
            </div>
        </main>
    </div>
    
    @livewireScripts
</body>
</html> 