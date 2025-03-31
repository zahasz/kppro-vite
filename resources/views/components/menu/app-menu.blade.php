@php
// Definicja zmiennych menu wspólnych dla całej aplikacji
$isAdmin = auth()->user()->can('admin');

// Główne elementy menu użytkownika
$userMenuItems = [
    [
        'route' => 'dashboard',
        'icon' => 'fas fa-home',
        'label' => 'Panel Główny',
        'routeMatch' => 'dashboard'
    ],
    [
        'route' => 'finances.index',
        'icon' => 'fas fa-file-invoice-dollar',
        'label' => 'Finanse',
        'routeMatch' => 'finances.*'
    ],
    [
        'route' => 'invoices.index',
        'icon' => 'fas fa-file-invoice',
        'label' => 'Faktury',
        'routeMatch' => 'invoices.*'
    ],
    [
        'route' => 'warehouse.index',
        'icon' => 'fas fa-warehouse',
        'label' => 'Magazyn',
        'routeMatch' => 'warehouse.*'
    ],
    [
        'route' => 'contractors.index',
        'icon' => 'fas fa-users',
        'label' => 'Kontrahenci',
        'routeMatch' => 'contractors.*'
    ],
    [
        'route' => 'tasks.index',
        'icon' => 'fas fa-tasks',
        'label' => 'Zadania',
        'routeMatch' => 'tasks.*'
    ],
    [
        'route' => 'contracts.index',
        'icon' => 'fas fa-file-contract',
        'label' => 'Umowy',
        'routeMatch' => 'contracts.*'
    ],
    [
        'route' => 'estimates.index',
        'icon' => 'fas fa-calculator',
        'label' => 'Kosztorysy',
        'routeMatch' => 'estimates.*'
    ]
];

// Elementy menu w sekcji ustawień
$settingItems = [
    [
        'route' => 'profile.edit',
        'icon' => 'fas fa-user-circle',
        'label' => 'Profil',
        'routeMatch' => 'profile.edit'
    ],
    [
        'route' => 'subscription',
        'icon' => 'fas fa-crown',
        'label' => 'Subskrypcja',
        'routeMatch' => 'subscription*'
    ],
];

// Element Panel administratora
$adminDashboardItem = [
    'route' => 'admin.dashboard',
    'icon' => 'fas fa-tachometer-alt',
    'label' => 'Panel administratora',
    'routeMatch' => 'admin.dashboard'
];

// Grupy menu dla panelu administratora
$adminGroups = [
    [
        'name' => 'Użytkownicy',
        'icon' => 'fas fa-users',
        'items' => [
            [
                'route' => 'admin.users.index',
                'icon' => 'fas fa-users-cog',
                'label' => 'Użytkownicy',
                'routeMatch' => 'admin.users.*'
            ],
            [
                'route' => 'admin.roles.index',
                'icon' => 'fas fa-shield-alt',
                'label' => 'Role',
                'routeMatch' => 'admin.roles.*'
            ],
            [
                'route' => 'admin.permissions.index',
                'icon' => 'fas fa-key',
                'label' => 'Uprawnienia',
                'routeMatch' => 'admin.permissions.*'
            ],
            [
                'route' => 'admin.system.login-history',
                'icon' => 'fas fa-history',
                'label' => 'Historia logowania',
                'routeMatch' => 'admin.system.login-history'
            ],
        ],
    ],
    [
        'name' => 'Subskrypcje',
        'icon' => 'fas fa-credit-card',
        'items' => [
            [
                'route' => 'admin.subscriptions.index',
                'icon' => 'fas fa-list',
                'label' => 'Plany subskrypcji',
                'routeMatch' => 'admin.subscriptions.index'
            ],
            [
                'route' => 'admin.subscriptions.users',
                'icon' => 'fas fa-user-tag',
                'label' => 'Subskrypcje użytkowników',
                'routeMatch' => 'admin.subscriptions.users'
            ],
            [
                'route' => 'admin.subscriptions.stats',
                'icon' => 'fas fa-chart-pie',
                'label' => 'Statystyki subskrypcji',
                'routeMatch' => 'admin.subscriptions.stats'
            ],
        ],
    ],
    [
        'name' => 'Faktury',
        'icon' => 'fas fa-file-invoice',
        'items' => [
            [
                'route' => 'admin.billing.invoices',
                'icon' => 'fas fa-file-alt',
                'label' => 'Lista faktur',
                'routeMatch' => 'admin.billing.invoices'
            ],
            [
                'route' => 'admin.billing.statistics',
                'icon' => 'fas fa-chart-pie',
                'label' => 'Statystyki faktur',
                'routeMatch' => 'admin.billing.statistics'
            ],
            [
                'route' => 'admin.billing.settings',
                'icon' => 'fas fa-cog',
                'label' => 'Ustawienia faktur',
                'routeMatch' => 'admin.billing.settings'
            ],
            [
                'route' => 'admin.billing.generate',
                'icon' => 'fas fa-sync',
                'label' => 'Generowanie faktur',
                'routeMatch' => 'admin.billing.generate'
            ],
        ],
    ],
    [
        'name' => 'Finanse', 
        'icon' => 'fas fa-money-bill-wave',
        'items' => [
            [
                'route' => 'admin.subscriptions.payments',
                'icon' => 'fas fa-dollar-sign',
                'label' => 'Historia płatności',
                'routeMatch' => 'admin.subscriptions.payments'
            ],
            [
                'route' => 'admin.revenue.dashboard',
                'icon' => 'fas fa-chart-line',
                'label' => 'Statystyki przychodów',
                'routeMatch' => 'admin.revenue.dashboard'
            ],
            [
                'route' => 'admin.revenue.monthly',
                'icon' => 'fas fa-calendar-alt',
                'label' => 'Raporty miesięczne',
                'routeMatch' => 'admin.revenue.monthly'
            ],
            [
                'route' => 'admin.revenue.annual',
                'icon' => 'fas fa-chart-bar',
                'label' => 'Raporty roczne',
                'routeMatch' => 'admin.revenue.annual'
            ],
        ],
    ],
    [
        'name' => 'System',
        'icon' => 'fas fa-cogs',
        'items' => [
            [
                'route' => 'admin.system.logs',
                'icon' => 'fas fa-clipboard-list',
                'label' => 'Logi systemowe',
                'routeMatch' => 'admin.system.logs'
            ],
            [
                'route' => 'admin.system.info',
                'icon' => 'fas fa-info-circle',
                'label' => 'Informacje o systemie',
                'routeMatch' => 'admin.system.info'
            ],
            [
                'route' => 'admin.system.backup',
                'icon' => 'fas fa-database',
                'label' => 'Kopie zapasowe',
                'routeMatch' => 'admin.system.backup'
            ],
        ],
    ],
];

// Klasy CSS dla elementów menu - Używamy ciemnego stalowego granatu
$menuClasses = [
    'item' => [
        'normal' => 'flex items-center px-3 py-2 rounded-md text-white hover:bg-steel-blue-700/70 transition-all duration-150 mb-1 group',
        'active' => 'flex items-center px-3 py-2 rounded-md bg-steel-blue-700 text-white border-l-4 border-steel-blue-300 shadow-sm transition-all duration-150 mb-1 group'
    ],
    'icon' => [
        'normal' => 'w-5 h-5 text-center text-white/70 group-hover:text-white',
        'active' => 'w-5 h-5 text-center text-white'
    ],
    'group' => [
        'header' => 'flex items-center justify-between px-3 py-2 text-xs uppercase tracking-wider text-white/70 font-medium transition-colors duration-150 rounded-md hover:bg-steel-blue-700/30',
        'headerActive' => 'flex items-center justify-between px-3 py-2 text-xs uppercase tracking-wider text-white font-semibold transition-colors duration-150 rounded-md bg-steel-blue-700/30'
    ],
    'section' => 'px-3 py-1 text-xs uppercase tracking-wider text-white/60 font-semibold mt-6 mb-2',
    'divider' => 'mt-6 pt-4 border-t border-steel-blue-700/40'
];

// Funkcja do sprawdzania, czy którykolwiek element w grupie jest aktywny
function isGroupActive($items) {
    foreach ($items as $item) {
        if (request()->routeIs($item['routeMatch'])) {
            return true;
        }
    }
    return false;
}

// Sprawdzenie aktualnego kontekstu menu
$isAdminContext = request()->segment(1) === 'admin';
@endphp

@if($isAdminContext)
    <!-- Menu dla panelu administratora -->
    <!-- Główny element nawigacji - Panel administratora -->
    <a href="{{ route($adminDashboardItem['route']) }}" 
       data-route="{{ $adminDashboardItem['routeMatch'] }}"
       class="{{ request()->routeIs($adminDashboardItem['routeMatch']) 
       ? $menuClasses['item']['active']
       : $menuClasses['item']['normal'] }}">
        <i class="{{ $adminDashboardItem['icon'] }} {{ request()->routeIs($adminDashboardItem['routeMatch']) 
        ? $menuClasses['icon']['active'] 
        : $menuClasses['icon']['normal'] }}"></i>
        <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen">{{ $adminDashboardItem['label'] }}</span>
    </a>

    <!-- Grupy menu -->
    @foreach($adminGroups as $group)
        <div x-data="{ open: {{ isGroupActive($group['items']) ? 'true' : 'false' }} }" class="mt-5">
            <!-- Nagłówek grupy -->
            <button 
                @click="open = !open" 
                x-show="sidebarOpen"
                class="{{ isGroupActive($group['items']) ? $menuClasses['group']['headerActive'] : $menuClasses['group']['header'] }}"
            >
                <div class="flex items-center">
                    <i class="{{ $group['icon'] }} w-4 h-4 mr-2"></i>
                    <span>{{ $group['name'] }}</span>
                </div>
                <i class="fas fa-chevron-down transform transition-transform duration-200"
                   :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Małe menu dla zminimalizowanego sidebara -->
            <div x-show="!sidebarOpen" class="px-3 py-2 text-xs uppercase tracking-wider text-white/70 font-medium text-center">
                <i class="{{ $group['icon'] }} w-5 h-5"></i>
            </div>
            
            <!-- Elementy grupy -->
            <div x-show="open || !sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 space-y-1 px-2" x-cloak>
                @foreach($group['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                       data-route="{{ $item['routeMatch'] }}"
                       class="{{ request()->routeIs($item['routeMatch']) 
                       ? $menuClasses['item']['active'] 
                       : $menuClasses['item']['normal'] }}"
                    >
                        <i class="{{ $item['icon'] }} {{ request()->routeIs($item['routeMatch']) 
                        ? $menuClasses['icon']['active'] 
                        : $menuClasses['icon']['normal'] }}"></i>
                        <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Link do aplikacji użytkownika -->
    <div class="{{ $menuClasses['divider'] }}">
        <a href="{{ route('dashboard') }}" class="{{ $menuClasses['item']['normal'] }}">
            <i class="fas fa-arrow-left {{ $menuClasses['icon']['normal'] }}"></i>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Powrót do aplikacji</span>
        </a>
    </div>
@else
    <!-- Menu dla panelu użytkownika -->
    <!-- Główne menu -->
    <div class="space-y-1 mb-4">
        @foreach($userMenuItems as $item)
            <a href="{{ route($item['route']) }}" 
               class="{{ request()->routeIs($item['routeMatch']) 
               ? $menuClasses['item']['active']
               : $menuClasses['item']['normal'] }}">
                <i class="{{ $item['icon'] }} {{ request()->routeIs($item['routeMatch']) 
                ? $menuClasses['icon']['active'] 
                : $menuClasses['icon']['normal'] }}"></i>
                <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Ustawienia -->
    <div class="{{ $menuClasses['divider'] }}">
        <div x-show="sidebarOpen" class="{{ $menuClasses['section'] }}">Ustawienia</div>
        <div x-show="!sidebarOpen" class="block text-xs uppercase tracking-wider text-white/50 font-medium px-3 py-1">
            Ust.
        </div>
        
        <div class="space-y-1 mt-2">
            @foreach($settingItems as $item)
                <a href="{{ route($item['route']) }}" 
                   class="{{ request()->routeIs($item['routeMatch']) 
                   ? $menuClasses['item']['active']
                   : $menuClasses['item']['normal'] }}">
                    <i class="{{ $item['icon'] }} {{ request()->routeIs($item['routeMatch']) 
                    ? $menuClasses['icon']['active'] 
                    : $menuClasses['icon']['normal'] }}"></i>
                    <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Przycisk do panelu administratora dla uprawnionych użytkowników -->
    @if($isAdmin)
        <div class="{{ $menuClasses['divider'] }}">
            <a href="{{ route('admin.dashboard') }}" class="{{ $menuClasses['item']['normal'] }}">
                <i class="fas fa-user-shield {{ $menuClasses['icon']['normal'] }}"></i>
                <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Panel administratora</span>
            </a>
        </div>
    @endif
@endif 