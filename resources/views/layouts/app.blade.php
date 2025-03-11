<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        <script>
            window.routes = {
                'admin.roles.store': '{{ route('admin.roles.store') }}',
                'admin.roles.update': '{{ url('/admin/roles/{role}') }}',
                'admin.roles.destroy': '{{ url('/admin/roles/{role}') }}'
            };
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @yield('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.sidebar')

            <!-- Główna zawartość -->
            <div class="ml-[250px] min-h-screen">
                <!-- Górny pasek -->
                <div class="bg-white shadow-sm border-b">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <!-- Przycisk menu dla urządzeń mobilnych -->
                            <button class="menu-button md:hidden text-gray-600 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h2 class="text-xl font-semibold text-gray-800">@yield('title', config('app.name'))</h2>
                        </div>
                        
                        <!-- Menu użytkownika -->
                        <div class="flex items-center space-x-4">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <span class="text-sm font-medium">{{ Auth::user()->name ?? 'Gość' }}</span>
                                    <img src="{{ Auth::user()->avatar_url }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-8 h-8 rounded-full">
                                </button>

                                <!-- Menu rozwijane -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 py-1 bg-white rounded-md shadow-lg">
                                    
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Edytuj profil
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Wyloguj się
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zawartość strony -->
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
