<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Wsparcie dla Turbo -->
        <meta name="turbo-cache-control" content="no-preview">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire -->
        @livewireStyles
        
        <!-- Custom styles -->
        <style>
            [x-cloak] { display: none !important; }
            
            /* Scrollbar styling */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: rgb(var(--color-gray-100));
                border-radius: 3px;
            }
            
            ::-webkit-scrollbar-thumb {
                background: rgb(var(--color-gray-300));
                border-radius: 3px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: rgb(var(--color-gray-400));
            }
            
            .dark ::-webkit-scrollbar-track {
                background: rgb(var(--color-gray-800));
            }
            
            .dark ::-webkit-scrollbar-thumb {
                background: rgb(var(--color-gray-600));
            }
            
            .dark ::-webkit-scrollbar-thumb:hover {
                background: rgb(var(--color-gray-500));
            }
            
            .turbo-loading::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background-color: rgb(79, 70, 229);
                animation: loading 1s infinite;
                z-index: 9999;
            }
            
            @keyframes loading {
                0% { width: 0; }
                20% { width: 20%; }
                40% { width: 40%; }
                60% { width: 60%; }
                80% { width: 80%; }
                100% { width: 100%; }
            }
        </style>
        
        @stack('head')
    </head>
    <body class="font-sans antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen">
            {{ $slot }}
        </div>
        
        @livewireScripts
        
        @stack('scripts')
    </body>
</html> 