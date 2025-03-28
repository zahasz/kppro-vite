<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Panel Administratora</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Dodatkowe style -->
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --steel-blue-50: #f5f7fa;
            --steel-blue-100: #e4eaf3;
            --steel-blue-200: #d0dbe9;
            --steel-blue-300: #b1c2d9;
            --steel-blue-400: #8fa3c4;
            --steel-blue-500: #6d86ae;
            --steel-blue-600: #546e95;
            --steel-blue-700: #445a7a;
            --steel-blue-800: #3a4b66;
            --steel-blue-900: #2c374a;
            --steel-blue-950: #1e2533;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-56 bg-steel-blue-900 border-r border-steel-blue-800 hidden md:block">
            <div class="px-4 py-3 flex items-center justify-between border-b border-steel-blue-800">
                <div class="font-bold text-base text-white">Panel Administratora</div>
            </div>
            <nav class="mt-3 space-y-0.5 px-2">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                    <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>

                <div class="mt-3 pt-3 border-t border-steel-blue-800">
                    <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Użytkownicy</h3>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Użytkownicy
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Role
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Uprawnienia
                        </a>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-steel-blue-800">
                    <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Subskrypcje</h3>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.subscriptions.index') }}" class="{{ request()->routeIs('admin.subscriptions.index') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Plany subskrypcji
                        </a>
                        <a href="{{ route('admin.subscriptions.alpine') }}" class="{{ request()->routeIs('admin.subscriptions.alpine') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2l.001 6L10 12l-3.999 4.001L6 22H18v-6l-4-4 4-3.999V2H6z" />
                            </svg>
                            Plany Alpine.js
                        </a>
                        <a href="{{ route('admin.subscriptions.users') }}" class="{{ request()->routeIs('admin.subscriptions.users') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Subskrypcje użytkowników
                        </a>
                        <a href="{{ route('admin.subscriptions.payments') }}" class="{{ request()->routeIs('admin.subscriptions.payments') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historia płatności
                        </a>
                        <a href="{{ route('admin.subscriptions.notifications') }}" class="{{ request()->routeIs('admin.subscriptions.notifications') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Powiadomienia
                        </a>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-t border-steel-blue-800">
                    <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Przychody</h3>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.revenue.dashboard') }}" class="{{ request()->routeIs('admin.revenue.dashboard') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statystyki przychodów
                        </a>
                        <a href="{{ route('admin.revenue.monthly') }}" class="{{ request()->routeIs('admin.revenue.monthly') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Raporty miesięczne
                        </a>
                        <a href="{{ route('admin.revenue.annual') }}" class="{{ request()->routeIs('admin.revenue.annual') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Raporty roczne
                        </a>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-700">
                    <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">System</h3>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.system.logs') }}" class="{{ request()->routeIs('admin.system.logs') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Logi systemowe
                        </a>
                        <a href="{{ route('admin.system.info') }}" class="{{ request()->routeIs('admin.system.info') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informacje o systemie
                        </a>
                        <a href="{{ route('admin.system.backup') }}" class="{{ request()->routeIs('admin.system.backup') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Kopie zapasowe
                        </a>
                        <a href="{{ route('admin.system.login-history') }}" class="{{ request()->routeIs('admin.system.login-history') ? 'bg-steel-blue-800 text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white' }} group flex items-center px-2 py-1.5 text-sm font-medium rounded-md">
                            <svg class="text-slate-400 group-hover:text-slate-300 mr-3 flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historia logowania
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Mobilny sidebar -->
        <div x-data="{ open: false }" class="md:hidden">
            <div x-show="open" class="fixed inset-0 z-40 flex" x-cloak>
                <div @click="open = false" x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0">
                    <div class="absolute inset-0 bg-steel-blue-950 opacity-75"></div>
                </div>
                <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative max-w-xs w-full bg-steel-blue-900 pt-5 pb-4 flex-1 flex flex-col">
                    <div class="absolute top-0 right-0 -mr-14 p-1">
                        <button x-show="open" @click="open = false" class="h-12 w-12 rounded-full flex items-center justify-center focus:outline-none focus:bg-steel-blue-800">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex-shrink-0 px-4 flex items-center">
                        <div class="font-bold text-xl text-white">Panel Administratora</div>
                    </div>
                    <div class="mt-5 flex-1 h-0 overflow-y-auto">
                        <nav class="px-2 space-y-1">
                            <!-- Mobile nav items (same as desktop) -->
                        </nav>
                    </div>
                </div>
                <div class="flex-shrink-0 w-14"></div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-steel-blue-900 shadow-sm z-10">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div>
                        <button @click="open = true" type="button" class="md:hidden -ml-0.5 -mt-0.5 h-10 w-10 inline-flex items-center justify-center rounded-md text-slate-300 hover:text-white focus:outline-none">
                            <span class="sr-only">Otwórz sidebar</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-base font-medium text-white hidden md:inline-block">{{ $header ?? 'Panel administratora' }}</h1>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="p-1 rounded-full text-slate-300 hover:text-white focus:outline-none">
                                <span class="sr-only">Powiadomienia</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1" x-cloak>
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-900">Powiadomienia</h3>
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <!-- Lista powiadomień -->
                                    <div class="py-2 px-4 text-sm text-gray-700 border-b border-gray-100">
                                        <p class="font-medium">Nowa subskrypcja</p>
                                        <p class="text-xs text-gray-500">Jan Kowalski wykupił Plan Premium</p>
                                        <p class="text-xs text-gray-400 mt-1">2 minuty temu</p>
                                    </div>
                                </div>
                                <div class="py-1 px-4 text-xs text-center border-t border-gray-100">
                                    <a href="{{ route('admin.subscriptions.notifications') }}" class="text-steel-blue-700 hover:text-steel-blue-900">Zobacz wszystkie</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <div>
                                <button @click="open = !open" type="button" class="bg-steel-blue-800 rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Otwórz menu użytkownika</span>
                                    <div class="h-8 w-8 rounded-full bg-steel-blue-800 flex items-center justify-center text-white">
                                        {{ auth()->user()->name[0] }}
                                    </div>
                                </button>
                            </div>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" x-cloak>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Twój profil</a>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Powrót do aplikacji</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Wyloguj</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-100">
                <!-- Page content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    @if (session('success'))
                        <div class="bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 p-3 mb-4 rounded shadow-sm" role="alert">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-3 mb-4 rounded shadow-sm" role="alert">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-3 mb-4 rounded shadow-sm" role="alert">
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
        </div>
    </div>
</body>
</html> 