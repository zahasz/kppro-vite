@php
// Definicja głównych elementów menu
$menuItems = [
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

// Wyciągnięcie elementu Panel administratora z grupy Użytkownicy
$adminDashboardItem = [
    'route' => 'admin.dashboard',
    'icon' => 'fas fa-tachometer-alt',
    'label' => 'Panel administratora',
    'routeMatch' => 'admin.dashboard'
];

// Aktualizacja grupy Użytkownicy bez elementu Panel administratora
$adminGroups = [
    'Użytkownicy' => [
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
    'Subskrypcje' => [
        [
            'route' => 'admin.subscriptions.index',
            'icon' => 'fas fa-credit-card',
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
    'Finanse' => [
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
    'System' => [
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
];
@endphp

<!-- Menu Items -->
<div class="py-5 space-y-1 px-3 h-full overflow-y-auto pb-20" style="max-height: calc(100vh - 70px);">
    @foreach($menuItems as $item)
        <x-sidebar-menu-item 
            :route="$item['route']" 
            :icon="$item['icon']" 
            :label="$item['label']" 
            :route-match="$item['routeMatch']" 
        />
    @endforeach

    @auth
    @can('admin')
    <div class="pt-4 mt-4 border-t border-[#44546A]/30">
        <h3 class="text-xs uppercase text-white/60 font-semibold px-4 mb-2 tracking-wider" x-show="sidebarOpen">Panel administratora</h3>
        
        <!-- Panel administratora jako oddzielny element -->
        <x-sidebar-menu-item 
            :route="$adminDashboardItem['route']" 
            :icon="$adminDashboardItem['icon']" 
            :label="$adminDashboardItem['label']" 
            :route-match="$adminDashboardItem['routeMatch']" 
        />
        
        @foreach($adminGroups as $groupName => $items)
            <div x-data="{ open: false }" class="mt-2">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-[#44546A]/40 rounded-md transition-all duration-150" x-show="sidebarOpen">
                    <span class="text-xs uppercase text-white/70 font-medium tracking-wider">{{ $groupName }}</span>
                    <i class="fas fa-chevron-down text-white/60 transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                </button>
                
                <!-- Na wąskim pasku bocznym zawsze pokazuj tytuł grupy -->
                <h4 class="text-xs uppercase text-white/50 font-medium px-4 py-1 mb-1 tracking-wider" x-show="!sidebarOpen">{{ $groupName }}</h4>
                
                <div x-show="open || !sidebarOpen" x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 transform scale-95" 
                     x-transition:enter-end="opacity-100 transform scale-100" 
                     class="pl-2">
                    @foreach($items as $item)
                    <x-sidebar-menu-item 
                        :route="$item['route']" 
                        :icon="$item['icon']" 
                        :label="$item['label']" 
                        :route-match="$item['routeMatch']" 
                    />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endcan
    @endauth
</div>

<!-- Sign Out -->
@auth
<div class="absolute bottom-0 w-full border-t border-[#44546A]/30 bg-[#44546A]/90">
    <form method="POST" action="{{ route('logout') }}" class="block">
        @csrf
        <button type="submit" class="flex items-center px-6 py-3 w-full text-white hover:bg-[#44546A]/70 transition-all duration-150 group">
            <i class="fas fa-sign-out-alt w-5 h-5 text-center text-white/70 group-hover:text-white"></i>
            <span class="ml-3 text-sm font-medium group-hover:text-white" x-show="sidebarOpen">Wyloguj się</span>
        </button>
    </form>
</div>
@endauth 