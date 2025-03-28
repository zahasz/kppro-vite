<!-- Menu Items -->
<div class="py-5 space-y-1 px-3">
    <a href="{{ route('dashboard') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('dashboard') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-home w-5 h-5 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('dashboard') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Panel Główny</span>
    </a>

    <a href="{{ route('finances.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('finances.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-file-invoice-dollar w-5 h-5 text-center {{ request()->routeIs('finances.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('finances.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Finanse</span>
    </a>

    <a href="{{ route('invoices.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('invoices.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-file-invoice w-5 h-5 text-center {{ request()->routeIs('invoices.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('invoices.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Faktury</span>
    </a>

    <a href="{{ route('warehouse.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('warehouse.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-warehouse w-5 h-5 text-center {{ request()->routeIs('warehouse.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('warehouse.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Magazyn</span>
    </a>

    <a href="{{ route('contractors.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('contractors.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-users w-5 h-5 text-center {{ request()->routeIs('contractors.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('contractors.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Kontrahenci</span>
    </a>

    <a href="{{ route('tasks.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('tasks.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-tasks w-5 h-5 text-center {{ request()->routeIs('tasks.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('tasks.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Zadania</span>
    </a>

    <a href="{{ route('contracts.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('contracts.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-file-contract w-5 h-5 text-center {{ request()->routeIs('contracts.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('contracts.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Umowy</span>
    </a>

    <a href="{{ route('estimates.index') }}" 
       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('estimates.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
        <i class="fas fa-calculator w-5 h-5 text-center {{ request()->routeIs('estimates.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
        <span class="ml-3 text-sm {{ request()->routeIs('estimates.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Kosztorysy</span>
    </a>

    @can('admin')
    <div class="pt-4 mt-4 border-t border-dark-700">
        <h3 class="text-xs uppercase text-gray-500 font-semibold px-4 mb-2 tracking-wider" x-show="sidebarOpen">Panel administratora</h3>
        
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-tachometer-alt w-5 h-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Statystyki</span>
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-users-cog w-5 h-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.users.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Użytkownicy</span>
        </a>
        
        <a href="{{ route('admin.subscriptions.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.subscriptions.index') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-credit-card w-5 h-5 text-center {{ request()->routeIs('admin.subscriptions.index') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.subscriptions.index') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Subskrypcje</span>
        </a>
        
        <a href="{{ route('admin.subscriptions.alpine') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.subscriptions.alpine') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-file-invoice-dollar w-5 h-5 text-center {{ request()->routeIs('admin.subscriptions.alpine') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.subscriptions.alpine') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Subskrypcje Alpine</span>
        </a>
        
        <a href="{{ route('admin.revenue.dashboard') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.revenue.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-chart-line w-5 h-5 text-center {{ request()->routeIs('admin.revenue.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.revenue.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">Przychody</span>
        </a>
        
        <a href="{{ route('admin.system.info') }}" 
           class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('admin.system.*') ? 'bg-primary-700 text-white shadow-md font-medium border-l-4 border-primary-300 pl-3' : 'text-gray-300 hover:bg-dark-800 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-server w-5 h-5 text-center {{ request()->routeIs('admin.system.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            <span class="ml-3 text-sm {{ request()->routeIs('admin.system.*') ? 'font-semibold' : 'font-medium' }}" x-show="sidebarOpen">System</span>
        </a>
    </div>
    @endcan
</div>

<!-- Sign Out -->
<div class="absolute bottom-0 w-full border-t border-dark-700 bg-dark-950">
    <form method="POST" action="{{ route('logout') }}" class="block">
        @csrf
        <button type="submit" class="flex items-center px-6 py-3 w-full text-gray-300 hover:bg-dark-800 transition-all duration-150 group">
            <i class="fas fa-sign-out-alt w-5 h-5 text-center text-gray-400 group-hover:text-white"></i>
            <span class="ml-3 text-sm font-medium group-hover:text-white" x-show="sidebarOpen">Wyloguj się</span>
        </button>
    </form>
</div> 