@php
// Definicja głównych elementów menu użytkownika
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

// Elementy z dolnego menu, które teraz będą na samym dole menu bocznego
$bottomMenuItems = [
    [
        'route' => 'help',
        'icon' => 'fas fa-question-circle',
        'label' => 'Pomoc',
        'routeMatch' => 'help*'
    ],
    [
        'route' => 'settings',
        'icon' => 'fas fa-cog',
        'label' => 'Ustawienia',
        'routeMatch' => 'settings*'
    ],
    [
        'route' => 'reports',
        'icon' => 'fas fa-chart-bar',
        'label' => 'Raporty',
        'routeMatch' => 'reports*'
    ],
];

// Klasy CSS dla elementów menu
$menuItemClasses = [
    'normal' => 'flex items-center px-3 py-2 rounded-md text-white hover:bg-indigo-600/60 transition-all duration-150 mb-1 group',
    'active' => 'flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white border-l-4 border-indigo-300 shadow-sm transition-all duration-150 mb-1 group'
];

$sectionClasses = 'px-3 py-1 text-xs uppercase tracking-wider text-white/60 font-semibold mt-6 mb-2';
@endphp

<!-- Główne menu -->
<div class="space-y-1 mb-4">
    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="{{ request()->routeIs($item['routeMatch']) 
           ? $menuItemClasses['active']
           : $menuItemClasses['normal'] }}">
            <i class="{{ $item['icon'] }} w-5 h-5 text-center {{ request()->routeIs($item['routeMatch']) ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>

<!-- Ustawienia -->
<div class="mt-6 pt-4 border-t border-indigo-800/40">
    <div x-show="sidebarOpen" class="{{ $sectionClasses }}">Ustawienia</div>
    <div x-show="!sidebarOpen" class="block text-xs uppercase tracking-wider text-white/50 font-medium px-3 py-1">
        Ust.
    </div>
    
    <div class="space-y-1 mt-2">
        @foreach($settingItems as $item)
            <a href="{{ route($item['route']) }}" 
               class="{{ request()->routeIs($item['routeMatch']) 
               ? $menuItemClasses['active']
               : $menuItemClasses['normal'] }}">
                <i class="{{ $item['icon'] }} w-5 h-5 text-center {{ request()->routeIs($item['routeMatch']) ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- Elementy z dolnego menu przeniesione na dół menu bocznego -->
<div class="mt-6 pt-4 border-t border-indigo-800/40">
    <div x-show="sidebarOpen" class="{{ $sectionClasses }}">Szybki dostęp</div>
    <div x-show="!sidebarOpen" class="block text-xs uppercase tracking-wider text-white/50 font-medium px-3 py-1">
        Szybki
    </div>
    
    <div class="space-y-1 mt-2">
        @foreach($bottomMenuItems as $item)
            <a href="{{ route($item['route']) }}" 
               class="{{ request()->routeIs($item['routeMatch']) 
               ? $menuItemClasses['active']
               : $menuItemClasses['normal'] }}">
                <i class="{{ $item['icon'] }} w-5 h-5 text-center {{ request()->routeIs($item['routeMatch']) ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- Przycisk do panelu administratora dla uprawnionych użytkowników -->
@auth
@can('admin')
<div class="mt-6 pt-4 border-t border-indigo-800/40">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-white hover:bg-indigo-600/60 transition-all duration-150 group">
        <i class="fas fa-user-shield w-5 h-5 text-center text-white/70 group-hover:text-white"></i>
        <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Panel administratora</span>
    </a>
</div>
@endcan
@endauth 