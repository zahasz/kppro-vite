<!-- Menu boczne -->
<aside class="md:w-64 flex-shrink-0">
    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
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

                        <a href="{{ route('finances.budget.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 group">
                            <span class="w-5 h-5 rounded-lg bg-purple-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-wallet text-purple-600 text-xs"></i>
                            </span>
                            <span class="text-sm">Budżet</span>
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

                <!-- Księgowość -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-book text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Księgowość</span>
                </a>

                <!-- Magazyn -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-warehouse text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Magazyn</span>
                </a>

                <!-- Kontrahenci -->
                <a href="{{ route('contractors.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-users text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Kontrahenci</span>
                </a>

                <!-- Zadania -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-tasks text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Zadania</span>
                </a>

                <!-- Raporty -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Raporty</span>
                </a>

                <!-- Umowy -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-file-contract text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Umowy</span>
                </a>

                <!-- Kosztorysy -->
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-calculator text-gray-600 text-xs"></i>
                    </span>
                    <span class="text-sm">Kosztorysy</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Notatki -->
    <div class="mt-4">
        <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Notatki</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3 text-xs">
                        <div class="w-6 h-6 rounded-lg bg-yellow-100 bg-opacity-10 flex items-center justify-center mt-0.5">
                            <i class="fas fa-sticky-note text-yellow-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-700">Spotkanie z klientem</p>
                            <p class="text-gray-500 text-xs">Omówienie projektu - 15:00</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 text-xs">
                        <div class="w-6 h-6 rounded-lg bg-blue-100 bg-opacity-10 flex items-center justify-center mt-0.5">
                            <i class="fas fa-thumbtack text-blue-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-700">Przygotować raport</p>
                            <p class="text-gray-500 text-xs">Do końca tygodnia</p>
                        </div>
                    </div>
                    <button class="w-full flex items-center justify-center space-x-2 text-gray-700 hover:text-gray-900 group mt-2">
                        <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-plus text-gray-600 text-xs"></i>
                        </span>
                        <span class="text-xs">Dodaj notatkę</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside> 