<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Panel Administratora</title>

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
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            <aside class="fixed left-0 top-0 h-full bg-[#44546A]/90 shadow-xl z-30 transition-all duration-300 border-r border-[#44546A]/30" 
                   :class="{'w-[280px]': sidebarOpen, 'w-[80px]': !sidebarOpen}">
                
                <!-- Logo i nazwa firmy -->
                <div class="flex flex-col items-center p-6 border-b border-[#44546A]/40">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-3 bg-white/10 shadow-lg flex items-center justify-center">
                        <img class="w-10 h-10 object-contain" src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="Logo firmy">
                    </div>
                    <span class="text-sm font-semibold text-white text-center w-full px-2 mt-1" x-show="sidebarOpen">
                        {{ auth()->user()->company?->name ?? config('app.name') }}
                    </span>
                </div>
                
                <!-- Menu Items -->
                @include('layouts.sidebar-livewire')
            </aside>
            
            <!-- Main Content -->
            <div class="transition-all duration-300" :class="{'pl-[280px]': sidebarOpen, 'pl-[80px]': !sidebarOpen}">
                <!-- Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="fixed top-5 left-[284px] bg-[#44546A] text-white p-2 rounded-full shadow-md z-40 hover:bg-[#44546A]/80 hover:text-white transition-all duration-200"
                        :class="{'left-[284px]': sidebarOpen, 'left-[84px]': !sidebarOpen}">
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Page Heading -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $header ?? 'Panel Administratora' }}
                        </h2>
                        
                        <!-- User dropdown -->
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-9 h-9 bg-[#44546A] rounded-full flex items-center justify-center text-white font-semibold mr-2 shadow-sm">
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
                                 class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-md shadow-lg z-20 border border-gray-200"
                                 x-cloak>
                                 
                                 <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                     <i class="far fa-user-circle mr-2"></i> Profil
                                 </a>
                                 
                                 <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                     <i class="fas fa-desktop mr-2"></i> Aplikacja
                                 </a>
                                 
                                 <div class="border-t border-gray-100 my-1"></div>
                                 
                                 <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Wyloguj
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
                <main class="py-6 bg-gray-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
                
                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-500">
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