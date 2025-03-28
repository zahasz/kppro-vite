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

// Definicja elementów menu administratora
$adminItems = [
    [
        'route' => 'admin.dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'label' => 'Statystyki',
        'routeMatch' => 'admin.dashboard'
    ],
    [
        'route' => 'admin.users.index',
        'icon' => 'fas fa-users-cog',
        'label' => 'Użytkownicy',
        'routeMatch' => 'admin.users.*'
    ],
    [
        'route' => 'admin.subscriptions.index',
        'icon' => 'fas fa-credit-card',
        'label' => 'Subskrypcje',
        'routeMatch' => 'admin.subscriptions.index'
    ],
    [
        'route' => 'admin.subscriptions.alpine',
        'icon' => 'fas fa-file-invoice-dollar',
        'label' => 'Subskrypcje Alpine',
        'routeMatch' => 'admin.subscriptions.alpine'
    ],
    [
        'route' => 'admin.revenue.dashboard',
        'icon' => 'fas fa-chart-line',
        'label' => 'Przychody',
        'routeMatch' => 'admin.revenue.*'
    ],
    [
        'route' => 'admin.system.info',
        'icon' => 'fas fa-server',
        'label' => 'System',
        'routeMatch' => 'admin.system.*'
    ]
];
@endphp

<!-- Menu Items -->
<div class="py-5 space-y-1 px-3">
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
        
        @foreach($adminItems as $item)
            <x-sidebar-menu-item 
                :route="$item['route']" 
                :icon="$item['icon']" 
                :label="$item['label']" 
                :route-match="$item['routeMatch']" 
            />
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