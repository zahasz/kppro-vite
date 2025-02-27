<!-- Menu boczne -->
<aside class="md:w-64 flex-shrink-0">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4">
            <div class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-home text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Panel Główny</span>
                </a>

                <!-- Finanse z podmenu -->
                <div class="space-y-2">
                    <a href="{{ route('finances.index') }}" class="flex items-center space-x-2 {{ request()->routeIs('finances.*') ? 'text-blue-600' : 'text-gray-700 hover:text-gray-900' }} group">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-file-invoice-dollar text-blue-600 text-xs"></i>
                        </span>
                        <span class="text-sm">Finanse</span>
                    </a>

                    @if(request()->routeIs('finances.*'))
                    <div class="pl-8 space-y-2">
                        <a href="{{ route('finances.incomes') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-green-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-plus text-green-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Przychody</span>
                        </a>
                        
                        <a href="{{ route('finances.expenses') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-red-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-minus text-red-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Koszta</span>
                        </a>
                        
                        <a href="{{ route('finances.invoices.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-blue-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-file-invoice text-blue-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Faktury</span>
                        </a>

                        <a href="{{ route('finances.accounting') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-purple-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-book text-purple-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Księgowość</span>
                        </a>
                        
                        <a href="{{ route('finances.reports') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-indigo-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-chart-pie text-indigo-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Raporty</span>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Kontrahenci -->
                <a href="{{ route('contractors.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-users text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Kontrahenci</span>
                </a>
            </div>
        </div>
    </div>
</aside> 