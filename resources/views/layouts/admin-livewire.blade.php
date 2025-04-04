<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Panel Administratora</title>

        <!-- Scripts - wczytanie Alpine.js przed renderowaniem -->
        <!-- Alpine jest już importowany przez Vite w app.js -->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire -->
        @livewireStyles
        
        <!-- Custom styles -->
        <style>
            [x-cloak] { display: none !important; }
            
            :root {
                /* Paleta kolorów biznesowych */
                --business-50: #f5f7fa;
                --business-100: #ebeef2;
                --business-200: #dde1e8;
                --business-300: #cbd0db;
                --business-400: #a2acc0;
                --business-500: #7886a2;
                --business-600: #5b6a88;
                --business-700: #44546A;
                --business-800: #384057;
                --business-900: #303748;
                --business-950: #1e222d;
                
                /* Akcenty pastelowe */
                --pastel-blue: #b8c5e2;
                --pastel-green: #bfe5bf;
                --pastel-orange: #f9d5a7;
                --pastel-red: #f8bfc3;
                --pastel-purple: #d0bfde;
            }
            
            /* Scrollbar styling */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 3px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
            
            /* Stany aktywne */
            .active-nav-item {
                background-color: var(--business-700);
                color: white;
                border-left: 4px solid var(--business-400);
            }

            /* Prevent FOUC (Flash of Unstyled Content) */
            html.loading {
                visibility: hidden;
            }
            
            /* Nowe style dla menu */
            .sidebar-gradient {
                background: linear-gradient(180deg, #44546A 0%, #303748 100%);
            }
            
            .page-gradient {
                background: linear-gradient(to right bottom, #f8fafc, #f1f5f9);
            }
            
            .btn-teal {
                background: linear-gradient(to right, #0ea5e9, #0284c7);
                color: white;
            }
            
            .btn-indigo {
                background: linear-gradient(to right, #6366f1, #4f46e5);
                color: white;
            }
        </style>

        <script>
            // Ustaw klasę loading na html, aby ukryć zawartość podczas ładowania
            document.documentElement.classList.add('loading');
            
            // Debugowanie - wersja widoku
            console.log('Admin Layout (Livewire) - v1.1');
            
            // Usuń klasę loading po załadowaniu
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    document.documentElement.classList.remove('loading');
                    console.log('Admin Layout (Livewire) - załadowany');
                }, 50);
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 page-gradient">
        <!-- Debugowanie -->
        <div class="fixed bottom-2 right-2 bg-yellow-300 text-yellow-900 text-sm p-2 z-50 border border-yellow-500 rounded shadow-lg">
            Debug v1.2 - Admin Livewire
        </div>
        
        <div class="min-h-screen" 
             x-data="{ 
                sidebarOpen: $persist(true).as('admin_sidebar_state'),
                init() {
                    // Nasłuchiwanie na zdarzenie odświeżenia sidebar
                    this.$el.closest('[data-section=admin]').addEventListener('refresh-sidebar', () => {
                        // Pobierz zapisany stan
                        const savedState = localStorage.getItem('admin_sidebar_state');
                        console.log('Otrzymano zdarzenie refresh-sidebar, zapisany stan:', savedState);
                        
                        // Aktualizuj stan tylko jeśli się różni
                        if (savedState === 'true' && !this.sidebarOpen) {
                            this.sidebarOpen = true;
                            console.log('Przywrócono stan menu: otwarty');
                        } else if (savedState === 'false' && this.sidebarOpen) {
                            this.sidebarOpen = false;
                            console.log('Przywrócono stan menu: zamknięty');
                        }
                    });
                }
             }" 
             data-section="admin">
            <!-- Sidebar -->
            <aside class="fixed left-0 top-0 h-full sidebar-gradient shadow-xl z-30 transition-all duration-300 border-r border-[#44546A]/30" 
                   :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}">
                
                <!-- Logo i nazwa firmy -->
                <div class="flex items-center p-4 mx-2 mt-2 mb-3 rounded-xl bg-white/5 backdrop-blur-sm">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg">
                        <img class="w-7 h-7 object-contain" src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="Logo firmy">
                    </div>
                    <span class="ml-3 text-sm font-semibold text-white truncate" x-show="sidebarOpen" x-cloak>
                        {{ auth()->user()->company?->name ?? config('app.name') }}
                    </span>
                </div>
                
                <!-- Menu Items -->
                <x-menu.admin-menu />
            </aside>
            
            <!-- Main Content -->
            <div class="transition-all duration-300" :class="{'pl-64': sidebarOpen, 'pl-16': !sidebarOpen}">
                <!-- Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="fixed top-5 z-40 shadow-lg transition-all duration-200 bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-2 rounded-full hover:shadow-xl"
                        :class="{'left-[246px]': sidebarOpen, 'left-[50px]': !sidebarOpen}">
                    <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Page Heading -->
                <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-20">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $header ?? 'Panel Administratora' }}
                        </h2>
                        
                        <!-- User dropdown -->
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-9 h-9 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold mr-2 shadow-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-xl shadow-lg z-20 border border-gray-100">
                                 
                                 <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                     <i class="far fa-user-circle w-5 text-center mr-2 text-indigo-500"></i> Profil
                                 </a>
                                 
                                 <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                     <i class="fas fa-desktop w-5 text-center mr-2 text-indigo-500"></i> Aplikacja
                                 </a>
                                 
                                 <div class="border-t border-gray-100 my-1"></div>
                                 
                                 <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt w-5 text-center mr-2 text-indigo-500"></i> Wyloguj
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div>
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Zaloguj</a>
                        </div>
                        @endauth
                    </div>
                </header>

                <!-- Page Content -->
                <main class="py-6 min-h-screen">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Komunikaty systemowe -->
                        @if (session('success'))
                            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
                
                <!-- Footer -->
                <footer class="bg-white/80 backdrop-blur-md border-t border-gray-200 py-4 text-center text-sm text-gray-500">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        &copy; {{ date('Y') }} {{ config('app.name') }} - Wszelkie prawa zastrzeżone
                    </div>
                </footer>
            </div>
        </div>
        
        @livewireScripts
        @stack('scripts')
    </body>
</html> 