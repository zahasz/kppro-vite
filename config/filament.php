<?php

return [
    'middleware' => [
        'base' => [
            \App\Http\Middleware\HandleInertiaRequests::class,
            'web',
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'auth' => [
            'auth',
            'verified',
        ],
    ],

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class,
        ],
    ],

    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
    ],

    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
    ],

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
    ],

    'livewire' => [
        'namespace' => 'App\\Filament',
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'layout' => [
        'actions' => [
            'modal' => [
                'actions' => [
                    'alignment' => 'left',
                    'are_sticky' => false,
                ],
            ],
        ],
        'forms' => [
            'actions' => [
                'alignment' => 'left',
                'are_sticky' => false,
            ],
            'have_inline_labels' => false,
        ],
    ],

    'default_navigation_sort' => 1,

    'default_navigation_group' => null,

    'navigation' => [
        'groups' => [
            'collapsible_on_desktop' => true,
        ],
    ],

    'max_content_width' => null,

    'home_url' => '/admin',

    'brand' => env('APP_NAME'),

    'favicon' => null,

    'discover_resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [
            \App\Filament\Resources\UserResource::class,
            \App\Filament\Resources\ContractorResource::class,
            \App\Filament\Resources\InvoiceResource::class,
            \App\Filament\Resources\EstimateResource::class,
            \App\Filament\Resources\ContractResource::class,
            \App\Filament\Resources\TaskResource::class,
            \App\Filament\Resources\WarehouseMaterialResource::class,
            \App\Filament\Resources\WarehouseEquipmentResource::class,
            \App\Filament\Resources\WarehouseToolResource::class,
            \App\Filament\Resources\WarehouseVehicleResource::class,
        ],
    ],

    'discover_pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [
            \App\Filament\Pages\Dashboard::class,
        ],
    ],

    'discover_widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\LatestInvoices::class,
            \App\Filament\Widgets\LatestEstimates::class,
            \App\Filament\Widgets\LatestTasks::class,
            \App\Filament\Widgets\LatestContracts::class,
            \App\Filament\Widgets\LatestWarehouseMaterials::class,
            \App\Filament\Widgets\LatestWarehouseEquipment::class,
            \App\Filament\Widgets\LatestWarehouseTools::class,
            \App\Filament\Widgets\LatestWarehouseVehicles::class,
        ],
    ],

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\LatestInvoices::class,
            \App\Filament\Widgets\LatestEstimates::class,
            \App\Filament\Widgets\LatestTasks::class,
            \App\Filament\Widgets\LatestContracts::class,
            \App\Filament\Widgets\LatestWarehouseMaterials::class,
            \App\Filament\Widgets\LatestWarehouseEquipment::class,
            \App\Filament\Widgets\LatestWarehouseTools::class,
            \App\Filament\Widgets\LatestWarehouseVehicles::class,
        ],
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'default_navigation_sort' => 1,

    'default_navigation_group' => null,

    'navigation' => [
        'groups' => [
            'collapsible_on_desktop' => true,
        ],
    ],

    'max_content_width' => null,

    'home_url' => '/admin',

    'brand' => env('APP_NAME'),

    'favicon' => null,
]; 