<x-app-layout>
    <div class="py-6">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">{{ __('Dashboard') }}</h2>
                <!-- Tu będzie menu logowania -->
            </div>
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Menu boczne -->
                <div class="md:w-72 flex-shrink-0">
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="space-y-4">
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-home text-gray-600"></i>
                                    </span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-file-invoice-dollar text-gray-600"></i>
                                    </span>
                                    <span>Finanse</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-book text-gray-600"></i>
                                    </span>
                                    <span>Księgowość</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-warehouse text-gray-600"></i>
                                    </span>
                                    <span>Magazyn</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-users text-gray-600"></i>
                                    </span>
                                    <span>Kontrahenci</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-tasks text-gray-600"></i>
                                    </span>
                                    <span>Zadania</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-chart-bar text-gray-600"></i>
                                    </span>
                                    <span>Raporty</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-file-contract text-gray-600"></i>
                                    </span>
                                    <span>Umowy</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 group">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-calculator text-gray-600"></i>
                                    </span>
                                    <span>Kosztorysy</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Główna zawartość -->
                <div class="flex-1">
                    <!-- Szybkie akcje -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mb-12">
                        <div class="p-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Szybkie akcje</h3>
                            <div class="flex flex-wrap gap-2">
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-file-invoice text-base"></i>
                                    <span>Nowa oferta</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-arrow-up text-base"></i>
                                    <span>Nowy przychód</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-arrow-down text-base"></i>
                                    <span>Nowy koszt</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-tasks text-base"></i>
                                    <span>Nowe zadanie</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-calculator text-base"></i>
                                    <span>Nowy kosztorys</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-file-invoice-dollar text-base"></i>
                                    <span>Nowa faktura</span>
                                </button>
                                <button class="btn flex items-center justify-center space-x-1.5 bg-blue-50 bg-opacity-10 hover:bg-opacity-20 text-blue-700 font-medium py-1.5 px-2 rounded-md transition-all duration-200 text-xs">
                                    <i class="fas fa-box text-base"></i>
                                    <span>Wydanie magazynowe</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statystyki - Przychody -->
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Przychody</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód miesięczny</h4>
                                    <p class="text-xl font-bold text-green-600">12 500 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 bg-opacity-10" style="width: 75%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">75% celu miesięcznego</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód księgowany</h4>
                                    <p class="text-xl font-bold text-green-600">8 200 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 bg-opacity-10" style="width: 65%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">65% całości przychodów</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód nieksięgowany</h4>
                                    <p class="text-xl font-bold text-green-600">4 300 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 bg-opacity-10" style="width: 35%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">35% całości przychodów</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Należności</h4>
                                    <p class="text-xl font-bold text-amber-600">3 800 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 bg-opacity-10" style="width: 30%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">30% przychodów do rozliczenia</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statystyki - Koszty -->
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Koszty</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt miesięczny</h4>
                                    <p class="text-xl font-bold text-red-600">8 300 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 bg-opacity-10" style="width: 45%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">45% budżetu miesięcznego</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt księgowany</h4>
                                    <p class="text-xl font-bold text-red-600">28 900 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 bg-opacity-10" style="width: 55%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">55% całości kosztów</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt nieksięgowany</h4>
                                    <p class="text-xl font-bold text-red-600">98 000 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 bg-opacity-10" style="width: 40%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">40% całości kosztów</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Zobowiązania</h4>
                                    <p class="text-xl font-bold text-red-600">12 400 zł</p>
                                    <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 bg-opacity-10" style="width: 25%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">25% kosztów do rozliczenia</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Moduły -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Moduły</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <!-- Finanse -->
                            <div class="bg-blue-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-blue-700 mb-1">Finanse</h4>
                                        <p class="text-sm text-blue-600 text-opacity-70">Zarządzanie finansami firmy</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-blue-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-file-invoice-dollar text-blue-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Księgowość -->
                            <div class="bg-indigo-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-indigo-700 mb-1">Księgowość</h4>
                                        <p class="text-sm text-indigo-600 text-opacity-70">Księgowość i dokumentacja</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-book text-indigo-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Magazyn -->
                            <div class="bg-orange-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-orange-700 mb-1">Magazyn</h4>
                                        <p class="text-sm text-orange-600 text-opacity-70">Zarządzanie magazynem</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-orange-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-warehouse text-orange-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Kontrahenci -->
                            <div class="bg-yellow-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-yellow-700 mb-1">Kontrahenci</h4>
                                        <p class="text-sm text-yellow-600 text-opacity-70">Baza kontrahentów</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-yellow-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-users text-yellow-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Zadania -->
                            <div class="bg-purple-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-purple-700 mb-1">Zadania</h4>
                                        <p class="text-sm text-purple-600 text-opacity-70">Zarządzanie zadaniami</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-purple-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-tasks text-purple-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Raporty -->
                            <div class="bg-cyan-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-cyan-700 mb-1">Raporty</h4>
                                        <p class="text-sm text-cyan-600 text-opacity-70">Analizy i raporty</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-cyan-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-chart-bar text-cyan-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Umowy -->
                            <div class="bg-red-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-red-700 mb-1">Umowy</h4>
                                        <p class="text-sm text-red-600 text-opacity-70">Zarządzanie umowami</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-red-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-file-contract text-red-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Kosztorysy -->
                            <div class="bg-pink-50 bg-opacity-5 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg group hover:bg-opacity-10 transition-all duration-200 cursor-pointer">
                                <div class="p-6 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-semibold text-pink-700 mb-1">Kosztorysy</h4>
                                        <p class="text-sm text-pink-600 text-opacity-70">Wyceny i kosztorysy</p>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-pink-100 bg-opacity-20 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                        <i class="fas fa-calculator text-pink-600 text-opacity-20 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ostatnie aktywności -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Ostatnie aktywności</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4 text-sm">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 bg-opacity-10 flex items-center justify-center">
                                        <i class="fas fa-file-invoice text-blue-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Nowa oferta została utworzona</p>
                                        <p class="text-gray-500">Oferta #123 - Projekt strony internetowej</p>
                                        <div class="mt-1 h-1 bg-blue-500 bg-opacity-10 rounded-full" style="width: 60%"></div>
                                    </div>
                                    <div class="text-gray-500">2 godziny temu</div>
                                </div>
                                <div class="flex items-center space-x-4 text-sm">
                                    <div class="w-10 h-10 rounded-full bg-green-100 bg-opacity-10 flex items-center justify-center">
                                        <i class="fas fa-arrow-up text-green-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Zarejestrowano nowy przychód</p>
                                        <p class="text-gray-500">Faktura #456 - 1500 zł</p>
                                        <div class="mt-1 h-1 bg-green-500 bg-opacity-10 rounded-full" style="width: 100%"></div>
                                    </div>
                                    <div class="text-gray-500">wczoraj</div>
                                </div>
                                <div class="flex items-center space-x-4 text-sm">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 bg-opacity-10 flex items-center justify-center">
                                        <i class="fas fa-tasks text-purple-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Nowe zadanie zostało przypisane</p>
                                        <p class="text-gray-500">Przygotowanie prezentacji dla klienta</p>
                                        <div class="mt-1 h-1 bg-purple-500 bg-opacity-10 rounded-full" style="width: 30%"></div>
                                    </div>
                                    <div class="text-gray-500">2 dni temu</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
