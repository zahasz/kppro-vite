@php
// Definicja elementu Panel administratora
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

// Klasy CSS dla elementów menu
$classes = [
    'item' => [
        'normal' => 'flex items-center px-3 py-2 rounded-md text-white hover:bg-[#44546A]/70 transition-all duration-150 mb-1 group',
        'active' => 'flex items-center px-3 py-2 rounded-md bg-[#44546A] text-white border-l-4 border-[#A2ACC0] shadow-sm transition-all duration-150 mb-1 group'
    ],
    'icon' => [
        'normal' => 'w-5 h-5 text-center text-white/70 group-hover:text-white',
        'active' => 'w-5 h-5 text-center text-white'
    ],
    'group' => [
        'header' => 'flex items-center justify-between px-3 py-2 text-xs uppercase tracking-wider text-white/70 font-medium transition-colors duration-150 rounded-md hover:bg-[#44546A]/30',
        'headerActive' => 'flex items-center justify-between px-3 py-2 text-xs uppercase tracking-wider text-white font-semibold transition-colors duration-150 rounded-md bg-[#44546A]/30'
    ]
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
@endphp

<!-- Główny element nawigacji - Panel administratora -->
<a href="{{ route($adminDashboardItem['route']) }}" 
   data-route="{{ $adminDashboardItem['routeMatch'] }}"
   class="{{ request()->routeIs($adminDashboardItem['routeMatch']) 
   ? $classes['item']['active']
   : $classes['item']['normal'] }}">
    <i class="{{ $adminDashboardItem['icon'] }} {{ request()->routeIs($adminDashboardItem['routeMatch']) 
    ? $classes['icon']['active'] 
    : $classes['icon']['normal'] }}"></i>
    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen">{{ $adminDashboardItem['label'] }}</span>
</a>

<div class="mt-4 space-y-3">
    @foreach($adminGroups as $group)
        @php
            $isActive = isGroupActive($group['items']);
        @endphp
        
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="space-y-1">
            <!-- Nagłówek grupy - widoczny tylko przy rozwiniętym menu -->
            <button @click="open = !open" 
                   x-show="sidebarOpen" 
                   class="{{ $isActive ? $classes['group']['headerActive'] : $classes['group']['header'] }}">
                <div class="flex items-center">
                    <i class="{{ $group['icon'] }} w-4 h-4 mr-2"></i>
                    <span>{{ $group['name'] }}</span>
                </div>
                <i class="fas fa-chevron-down text-white/60 transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
            </button>
            
            <!-- Ikona grupy - widoczna tylko przy zwiniętym menu -->
            <div x-show="!sidebarOpen" 
                 @click="open = !open"
                 class="flex justify-center px-3 py-1 {{ $isActive ? 'text-white' : 'text-white/60 hover:text-white' }} cursor-pointer">
                <i class="{{ $group['icon'] }} w-5 h-5"></i>
            </div>
            
            <!-- Elementy grupy -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="{{ !$isActive ? 'ml-2' : 'ml-0' }}">
                @foreach($group['items'] as $item)
                    @php
                        $isItemActive = request()->routeIs($item['routeMatch']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                        data-route="{{ $item['routeMatch'] }}"
                        class="{{ $isItemActive 
                        ? $classes['item']['active']
                        : $classes['item']['normal'] }}">
                        <i class="{{ $item['icon'] }} {{ $isItemActive 
                        ? $classes['icon']['active'] 
                        : $classes['icon']['normal'] }}"></i>
                        <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div class="fixed bottom-0 left-0 w-full bg-[#44546A]/90 border-t border-[#44546A]/40" 
     :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}">
    <!-- Przycisk powrotu do aplikacji -->
    <a href="{{ route('dashboard') }}" class="{{ $classes['item']['normal'] }} mb-0 rounded-none border-b border-[#44546A]/40">
        <i class="fas fa-arrow-left {{ $classes['icon']['normal'] }}"></i>
        <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Powrót do aplikacji</span>
    </a>

    <!-- Przycisk wylogowania -->
    <form method="POST" action="{{ route('logout') }}" class="block">
        @csrf
        <button type="submit" class="{{ $classes['item']['normal'] }} w-full rounded-none">
            <i class="fas fa-sign-out-alt {{ $classes['icon']['normal'] }}"></i>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Wyloguj się</span>
        </button>
    </form>
</div> 