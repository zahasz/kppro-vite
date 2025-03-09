<!-- Menu boczne -->
<div class="md:w-56 flex-shrink-0">
    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-4">
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-home {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Panel Główny</span>
            </a>

            <a href="{{ route('finances.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('finances.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('finances.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar {{ request()->routeIs('finances.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Finanse</span>
            </a>

            <a href="{{ route('warehouse.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('warehouse.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('warehouse.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-warehouse {{ request()->routeIs('warehouse.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Magazyn</span>
            </a>

            <a href="{{ route('contractors.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('contractors.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('contractors.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-users {{ request()->routeIs('contractors.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Kontrahenci</span>
            </a>

            <a href="{{ route('tasks.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('tasks.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-tasks {{ request()->routeIs('tasks.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Zadania</span>
            </a>

            <a href="{{ route('contracts.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('contracts.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('contracts.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-file-contract {{ request()->routeIs('contracts.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Umowy</span>
            </a>

            <a href="{{ route('estimates.index') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('estimates.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('estimates.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-calculator {{ request()->routeIs('estimates.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Kosztorysy</span>
            </a>

            @can('admin')
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-2 {{ request()->routeIs('admin.*') ? 'text-blue-500' : 'text-gray-600 hover:text-gray-900' }} group">
                <span class="w-6 h-6 rounded-lg {{ request()->routeIs('admin.*') ? 'bg-blue-100' : 'bg-gray-100' }} group-hover:bg-opacity-75 flex items-center justify-center">
                    <i class="fas fa-user-shield {{ request()->routeIs('admin.*') ? 'text-blue-600' : 'text-gray-700' }} text-xs"></i>
                </span>
                <span class="text-sm font-medium">Panel Admina</span>
            </a>
            @endcan
        </div>

        <!-- Separator i Notatki -->
        <div class="mt-6 pt-6 border-t border-gray-200 border-opacity-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Notatki</h3>
                <button class="text-xs text-blue-500 hover:text-blue-600 transition-colors duration-200">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div class="group">
                    <p class="text-xs text-gray-600 py-1">Spotkanie z klientem - 15:00</p>
                    <div class="h-px bg-gray-100"></div>
                </div>
                <div class="group">
                    <p class="text-xs text-gray-600 py-1">Deadline projektu - 20.03</p>
                    <div class="h-px bg-gray-100"></div>
                </div>
            </div>
        </div>
    </div>
</div> 