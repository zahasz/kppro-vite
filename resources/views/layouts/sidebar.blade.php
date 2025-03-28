<!-- Menu boczne -->
<div class="fixed left-0 top-0 h-full w-[280px] bg-gradient-to-b from-steel-blue-800 to-steel-blue-900 shadow-xl z-30">
    <!-- Logo i nazwa firmy -->
    <div class="flex flex-col items-center p-6 border-b border-steel-blue-700">
        <div class="w-20 h-20 rounded-full overflow-hidden mb-3 bg-white shadow-lg">
            <img class="w-full h-full object-cover" src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="Logo firmy">
        </div>
        <span class="text-sm font-semibold text-white text-center w-full px-2 mt-1">
            {{ auth()->user()->company?->name ?? config('app.name') }}
        </span>
    </div>

    <!-- Menu Items -->
    <div class="py-6 space-y-2 px-3">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-home w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Panel Główny</span>
        </a>

        <a href="{{ route('finances.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('finances.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-file-invoice-dollar w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Finanse</span>
        </a>

        <a href="{{ route('invoices.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-file-invoice w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Faktury</span>
        </a>

        <a href="{{ route('warehouse.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('warehouse.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-warehouse w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Magazyn</span>
        </a>

        <a href="{{ route('contractors.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('contractors.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-users w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Kontrahenci</span>
        </a>

        <a href="{{ route('tasks.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-tasks w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Zadania</span>
        </a>

        <a href="{{ route('contracts.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('contracts.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-file-contract w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Umowy</span>
        </a>

        <a href="{{ route('estimates.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('estimates.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
            <i class="fas fa-calculator w-6 h-6 text-center"></i>
            <span class="ml-4 text-base font-medium">Kosztorysy</span>
        </a>

        @can('admin')
        <div class="pt-4 mt-4 border-t border-steel-blue-700">
            <h3 class="text-xs uppercase text-steel-blue-300 font-semibold px-4 mb-2 tracking-wider">Panel administratora</h3>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
                <i class="fas fa-tachometer-alt w-6 h-6 text-center"></i>
                <span class="ml-4 text-base font-medium">Statystyki</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
                <i class="fas fa-users-cog w-6 h-6 text-center"></i>
                <span class="ml-4 text-base font-medium">Użytkownicy</span>
            </a>
            
            <a href="{{ route('admin.subscriptions.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.subscriptions.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
                <i class="fas fa-credit-card w-6 h-6 text-center"></i>
                <span class="ml-4 text-base font-medium">Subskrypcje</span>
            </a>
            
            <a href="{{ route('admin.revenue.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.revenue.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
                <i class="fas fa-chart-line w-6 h-6 text-center"></i>
                <span class="ml-4 text-base font-medium">Przychody</span>
            </a>
            
            <a href="{{ route('admin.system.info') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.system.*') ? 'bg-white text-steel-blue-800 shadow-md' : 'text-white hover:bg-steel-blue-700 hover:text-white' }} transition-all duration-200 group">
                <i class="fas fa-server w-6 h-6 text-center"></i>
                <span class="ml-4 text-base font-medium">System</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Sign Out -->
    <div class="absolute bottom-0 w-full border-t border-steel-blue-700 bg-steel-blue-900">
        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit" class="flex items-center px-6 py-4 w-full text-white hover:bg-steel-blue-700 transition-all duration-150 group">
                <i class="fas fa-sign-out-alt w-6 h-6 text-center group-hover:text-white"></i>
                <span class="ml-4 text-base font-medium group-hover:text-white">Wyloguj się</span>
            </button>
        </form>
    </div>
</div> 