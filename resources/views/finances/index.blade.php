<x-app-layout>
    <div class="py-6">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">{{ __('Finanse') }}</h2>
            </div>
            
            <!-- Główna zawartość -->
            <div class="flex-1">
                <!-- Statystyki -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Przychody -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-green-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód miesięczny</h4>
                            <p class="text-xl font-bold text-green-600">{{ number_format($totalIncome ?? 0, 2, ',', ' ') }} zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 bg-opacity-10" style="width: 75%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">75% celu miesięcznego</p>
                        </div>
                    </div>

                    <!-- Wydatki -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-red-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Wydatki</h4>
                            <p class="text-xl font-bold text-red-600">{{ number_format($totalExpenses ?? 0, 2, ',', ' ') }} zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 bg-opacity-10" style="width: 45%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">45% budżetu miesięcznego</p>
                        </div>
                    </div>

                    <!-- Zysk -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-blue-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Zysk</h4>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($profit ?? 0, 2, ',', ' ') }} zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 bg-opacity-10" style="width: 65%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">65% celu miesięcznego</p>
                        </div>
                    </div>

                    <!-- Należności -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-amber-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Należności</h4>
                            <p class="text-xl font-bold text-amber-600">{{ number_format($receivables ?? 0, 2, ',', ' ') }} zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 bg-opacity-10" style="width: 30%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">30% przychodów do rozliczenia</p>
                        </div>
                    </div>
                </div>

                <!-- Kafelki nawigacyjne -->
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <!-- Przychody -->
                    <a href="{{ route('finances.incomes') }}" class="bg-gradient-to-br from-blue-400/40 to-blue-500/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-400/60 hover:to-blue-500/60">
                        <div class="p-4 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-lg font-extrabold text-white mb-0.5 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Przychody</h4>
                                <p class="text-xs font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-plus text-white text-xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-12 -mt-12 transform rotate-45"></div>
                        </div>
                    </a>

                    <!-- Koszta -->
                    <a href="{{ route('finances.expenses') }}" class="bg-gradient-to-br from-blue-500/40 to-blue-600/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-500/60 hover:to-blue-600/60">
                        <div class="p-4 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-lg font-extrabold text-white mb-0.5 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Koszta</h4>
                                <p class="text-xs font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-minus text-white text-xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-12 -mt-12 transform rotate-45"></div>
                        </div>
                    </a>

                    <!-- Faktury -->
                    <a href="{{ route('finances.invoices.index') }}" class="bg-gradient-to-br from-blue-600/40 to-blue-700/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-600/60 hover:to-blue-700/60">
                        <div class="p-4 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-lg font-extrabold text-white mb-0.5 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Faktury</h4>
                                <p class="text-xs font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Zarządzanie</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-file-invoice text-white text-xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-12 -mt-12 transform rotate-45"></div>
                        </div>
                    </a>

                    <!-- Budżet -->
                    <a href="{{ route('finances.budget.index') }}" class="bg-gradient-to-br from-blue-700/40 to-blue-800/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-700/60 hover:to-blue-800/60">
                        <div class="p-4 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-lg font-extrabold text-white mb-0.5 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Budżet</h4>
                                <p class="text-xs font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Planowanie</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-wallet text-white text-xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-12 -mt-12 transform rotate-45"></div>
                        </div>
                    </a>

                    <!-- Raporty -->
                    <a href="{{ route('finances.reports') }}" class="bg-gradient-to-br from-blue-800/40 to-blue-900/40 overflow-hidden shadow-lg sm:rounded-lg group hover:shadow-xl transition-all duration-200 cursor-pointer transform hover:-translate-y-1 hover:from-blue-800/60 hover:to-blue-900/60">
                        <div class="p-4 flex items-center justify-between relative">
                            <div class="relative z-10">
                                <h4 class="text-lg font-extrabold text-white mb-0.5 drop-shadow-[0_2px_2px_rgba(0,0,0,0.3)]">Raporty</h4>
                                <p class="text-xs font-medium text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.2)]">Analizy</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-10 flex items-center justify-center relative z-10">
                                <i class="fas fa-chart-pie text-white text-xl"></i>
                            </div>
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-12 -mt-12 transform rotate-45"></div>
                        </div>
                    </a>
                </div>

                <!-- Sekcje informacyjne -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                    <!-- Sekcja Przychody -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-plus text-green-500 mr-2"></i>
                                Przychody
                            </h3>
                            <span class="text-xs text-gray-500">Ostatnie 30 dni</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Faktury wystawione</span>
                                <span class="font-semibold text-gray-800">{{ number_format($totalInvoices ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Średni przychód dzienny</span>
                                <span class="font-semibold text-gray-800">{{ number_format($avgDailyIncome ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Największa transakcja</span>
                                <span class="font-semibold text-gray-800">{{ number_format($maxIncome ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sekcja Koszta -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-minus text-red-500 mr-2"></i>
                                Koszta
                            </h3>
                            <span class="text-xs text-gray-500">Ostatnie 30 dni</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Faktury otrzymane</span>
                                <span class="font-semibold text-gray-800">{{ number_format($totalExpenseInvoices ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Średni wydatek dzienny</span>
                                <span class="font-semibold text-gray-800">{{ number_format($avgDailyExpense ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Największy wydatek</span>
                                <span class="font-semibold text-gray-800">{{ number_format($maxExpense ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sekcja Faktury -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice text-blue-500 mr-2"></i>
                                Faktury
                            </h3>
                            <span class="text-xs text-gray-500">Ostatnie 30 dni</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Wszystkie faktury</span>
                                <span class="font-semibold text-gray-800">{{ number_format($totalAllInvoices ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Faktury niezapłacone</span>
                                <span class="font-semibold text-gray-800">{{ number_format($unpaidInvoices ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Średni termin płatności</span>
                                <span class="font-semibold text-gray-800">{{ $avgPaymentTerm ?? 14 }} dni</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sekcja Budżet -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-wallet text-purple-500 mr-2"></i>
                                Budżet
                            </h3>
                            <span class="text-xs text-gray-500">Ostatnie 30 dni</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Wykorzystanie budżetu</span>
                                <span class="font-semibold text-gray-800">{{ number_format($budgetUsage ?? 0) }}%</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Oszczędności</span>
                                <span class="font-semibold text-gray-800">{{ number_format($savings ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Prognoza na koniec miesiąca</span>
                                <span class="font-semibold text-gray-800">{{ number_format($monthEndForecast ?? 0, 2, ',', ' ') }} zł</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 