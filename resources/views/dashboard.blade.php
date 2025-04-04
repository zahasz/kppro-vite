<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">{{ __('Panel główny') }}</h2>
            </div>

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
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-green-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód miesięczny</h4>
                            <p class="text-xl font-bold text-green-600">12 500 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 bg-opacity-10" style="width: 75%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">75% celu miesięcznego</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-green-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód księgowany</h4>
                            <p class="text-xl font-bold text-green-600">8 200 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 bg-opacity-10" style="width: 65%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">65% całości przychodów</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-green-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód nieksięgowany</h4>
                            <p class="text-xl font-bold text-green-600">4 300 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 bg-opacity-10" style="width: 35%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">35% całości przychodów</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-amber-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="p-3 relative z-10">
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
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-red-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt miesięczny</h4>
                            <p class="text-xl font-bold text-red-600">8 300 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 bg-opacity-10" style="width: 45%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">45% budżetu miesięcznego</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-red-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt księgowany</h4>
                            <p class="text-xl font-bold text-red-600">28 900 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 bg-opacity-10" style="width: 55%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">55% całości kosztów</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-red-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Koszt nieksięgowany</h4>
                            <p class="text-xl font-bold text-red-600">98 000 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 bg-opacity-10" style="width: 40%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">40% całości kosztów</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-amber-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Zobowiązania</h4>
                            <p class="text-xl font-bold text-amber-600">15 600 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 bg-opacity-10" style="width: 30%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">30% kosztów do rozliczenia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Moduły -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Moduły</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Finanse -->
                    <a href="{{ route('finances.index') }}" class="bg-gradient-to-br from-blue-400/40 to-blue-500/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-400/60 hover:to-blue-500/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Finanse</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie finansami firmy</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-file-invoice-dollar text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </a>
                    <!-- Księgowość -->
                    <div class="bg-gradient-to-br from-blue-500/40 to-blue-600/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-500/60 hover:to-blue-600/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Księgowość</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Księgowość i dokumentacja</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-book text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                    <!-- Magazyn -->
                    <div class="bg-gradient-to-br from-blue-600/40 to-blue-700/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-600/60 hover:to-blue-700/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Magazyn</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie magazynem</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-warehouse text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                    <!-- Kontrahenci -->
                    <a href="{{ route('contractors.index') }}" class="bg-gradient-to-br from-blue-700/40 to-blue-800/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-700/60 hover:to-blue-800/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Kontrahenci</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Baza kontrahentów</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-users text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </a>
                    <!-- Zadania -->
                    <div class="bg-gradient-to-br from-blue-800/40 to-blue-900/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-800/60 hover:to-blue-900/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Zadania</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie zadaniami</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-tasks text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                    <!-- Raporty -->
                    <div class="bg-gradient-to-br from-blue-400/40 to-blue-500/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-400/60 hover:to-blue-500/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Raporty</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Analizy i raporty</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-chart-bar text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                    <!-- Umowy -->
                    <div class="bg-gradient-to-br from-blue-500/40 to-blue-600/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-500/60 hover:to-blue-600/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Umowy</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie umowami</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-file-contract text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                    <!-- Kosztorysy -->
                    <div class="bg-gradient-to-br from-blue-600/40 to-blue-700/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-600/60 hover:to-blue-700/60">
                        <div class="p-6 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-2xl font-extrabold text-white mb-1 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Kosztorysy</h4>
                                <p class="text-sm font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Wyceny i kosztorysy</p>
                            </div>
                            <div class="w-20 h-20 rounded-2xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-calculator text-white text-4xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 transform rotate-45"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktywności i powiadomienia -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Ostatnie aktywności -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Ostatnie aktywności</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-blue-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Nowa oferta została utworzona</p>
                                    <p class="text-gray-500 text-xs">Oferta #123 - Projekt strony internetowej</p>
                                    <div class="mt-1 h-1 bg-blue-500 bg-opacity-10 rounded-full" style="width: 60%"></div>
                                </div>
                                <div class="text-gray-500 text-xs">2h temu</div>
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-green-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-arrow-up text-green-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Zarejestrowano nowy przychód</p>
                                    <p class="text-gray-500 text-xs">Faktura #456 - 1500 zł</p>
                                    <div class="mt-1 h-1 bg-green-500 bg-opacity-10 rounded-full" style="width: 100%"></div>
                                </div>
                                <div class="text-gray-500 text-xs">wczoraj</div>
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-purple-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-tasks text-purple-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Nowe zadanie zostało przypisane</p>
                                    <p class="text-gray-500 text-xs">Przygotowanie prezentacji dla klienta</p>
                                    <div class="mt-1 h-1 bg-purple-500 bg-opacity-10 rounded-full" style="width: 30%"></div>
                                </div>
                                <div class="text-gray-500 text-xs">2 dni temu</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Powiadomienia -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Powiadomienia</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-red-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-exclamation-circle text-red-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Zaległe płatności</p>
                                    <p class="text-gray-500 text-xs">3 faktury oczekują na płatność</p>
                                </div>
                                <div class="text-gray-500 text-xs">1h temu</div>
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Zbliżający się termin</p>
                                    <p class="text-gray-500 text-xs">Projekt XYZ - pozostało 2 dni</p>
                                </div>
                                <div class="text-gray-500 text-xs">4h temu</div>
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-full bg-green-100 bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Zadanie zakończone</p>
                                    <p class="text-gray-500 text-xs">Raport miesięczny został zatwierdzony</p>
                                </div>
                                <div class="text-gray-500 text-xs">wczoraj</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Witaj w panelu administracyjnym!") }}
                </div>
            </div>
            
            <!-- Dostępne moduły -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Dostępne moduły:</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @module('invoices')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-600">Faktury</h3>
                            <p class="mt-2 text-gray-600">Zarządzaj fakturami, twórz nowe i monitoruj płatności.</p>
                            <a href="{{ route('invoices.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Przejdź do faktur
                            </a>
                        </div>
                    @endmodule
                    
                    @module('products')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-600">Produkty</h3>
                            <p class="mt-2 text-gray-600">Zarządzaj produktami, cenami i stanami magazynowymi.</p>
                            <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Przejdź do produktów
                            </a>
                        </div>
                    @endmodule
                    
                    @module('warehouse')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-600">Magazyn</h3>
                            <p class="mt-2 text-gray-600">Zarządzaj magazynem, materiałami i wyposażeniem.</p>
                            <a href="{{ route('warehouse.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Przejdź do magazynu
                            </a>
                        </div>
                    @endmodule
                    
                    @module('finances')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-600">Finanse</h3>
                            <p class="mt-2 text-gray-600">Monitoruj finanse, przychody i wydatki.</p>
                            <a href="{{ route('finances.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Przejdź do finansów
                            </a>
                        </div>
                    @endmodule
                    
                    @module('contractors')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-600">Kontrahenci</h3>
                            <p class="mt-2 text-gray-600">Zarządzaj bazą kontrahentów.</p>
                            <a href="{{ route('contractors.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Przejdź do kontrahentów
                            </a>
                        </div>
                    @endmodule
                </div>
                
                <!-- Informacja o subskrypcji -->
                @if(auth()->user()->hasActiveSubscription())
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-green-600">Aktywna subskrypcja: {{ auth()->user()->currentSubscription()->plan->name }}</h3>
                        <p class="mt-2 text-gray-600">
                            Twoja subskrypcja jest aktywna do: {{ auth()->user()->currentSubscription()->end_date ? auth()->user()->currentSubscription()->end_date->format('d.m.Y') : 'Bezterminowo' }}
                        </p>
                    </div>
                @else
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-red-600">Brak aktywnej subskrypcji</h3>
                        <p class="mt-2 text-gray-600">
                            Wykup subskrypcję, aby uzyskać dostęp do wszystkich modułów aplikacji.
                        </p>
                        <a href="{{ route('subscription') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Sprawdź dostępne plany
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
