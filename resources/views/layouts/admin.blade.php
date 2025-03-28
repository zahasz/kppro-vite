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
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            <aside class="fixed left-0 top-0 h-full bg-[#44546A]/90 shadow-xl z-30 transition-all duration-300 border-r border-[#44546A]/30" 
                   :class="{'w-[280px]': sidebarOpen, 'w-[80px]': !sidebarOpen}">
                
                <!-- Logo i nazwa firmy -->
                <div class="flex flex-col items-center p-6 border-b border-[#44546A]/40">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-3 bg-white/10 shadow-lg flex items-center justify-center">
                        <img class="w-10 h-10 object-contain" src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="Logo firmy">
                    </div>
                    <span class="text-sm font-semibold text-gray-200 text-center w-full px-2 mt-1" x-show="sidebarOpen">
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
                        class="fixed top-5 left-[284px] bg-[#44546A] text-white p-2 rounded-full shadow-md z-40 hover:bg-[#44546A]/80 transition-all duration-200"
                        :class="{'left-[284px]': sidebarOpen, 'left-[84px]': !sidebarOpen}">
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Page Heading -->
                <header class="bg-white shadow-sm sticky top-0 z-20">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $header ?? 'Panel Administratora' }}
                        </h2>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs">3</span>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ auth()->user()->name }}">
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html> 