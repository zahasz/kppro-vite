<!-- Menu boczne -->
<div class="fixed left-0 top-0 h-full w-[250px] bg-[#2A3042] shadow-lg">
    <!-- Logo i nazwa firmy -->
    <div class="flex flex-col items-center p-4 border-b border-gray-700">
        <div class="w-16 h-16 rounded-full overflow-hidden mb-1 bg-white">
            <img class="w-full h-full object-cover" src="{{ auth()->user()->company?->logo_url ?? asset('images/logo.svg') }}" alt="Logo firmy">
        </div>
        <span class="text-xs font-medium text-gray-300 text-center w-full px-2">
            {{ auth()->user()->company?->name ?? config('app.name') }}
        </span>
    </div>

    <!-- Menu Items -->
    <div class="py-4 space-y-1">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-home w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Panel Główny</span>
        </a>

        <a href="{{ route('finances.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('finances.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-file-invoice-dollar w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Finanse</span>
        </a>

        <a href="{{ route('invoices.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('invoices.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-file-invoice w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Faktury</span>
        </a>

        <a href="{{ route('warehouse.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('warehouse.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-warehouse w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Magazyn</span>
        </a>

        <a href="{{ route('contractors.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('contractors.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-users w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Kontrahenci</span>
        </a>

        <a href="{{ route('tasks.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('tasks.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-tasks w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Zadania</span>
        </a>

        <a href="{{ route('contracts.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('contracts.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-file-contract w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Umowy</span>
        </a>

        <a href="{{ route('estimates.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('estimates.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-calculator w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Kosztorysy</span>
        </a>

        @can('admin')
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('admin.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-[#333c52] hover:text-white' }} transition-all duration-150">
            <i class="fas fa-user-shield w-5 h-5"></i>
            <span class="ml-3 text-sm font-medium">Panel Admina</span>
        </a>
        @endcan
    </div>

    <!-- Sign Out -->
    <div class="absolute bottom-0 w-full border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit" class="flex items-center px-4 py-3 w-full text-gray-300 hover:bg-[#333c52] hover:text-white transition-all duration-150">
                <i class="fas fa-sign-out-alt w-5 h-5"></i>
                <span class="ml-3 text-sm font-medium">Wyloguj się</span>
            </button>
        </form>
    </div>
</div> 