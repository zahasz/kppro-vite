@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Finanse') }}</h2>
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Menu boczne -->
            @include('layouts.sidebar')

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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Przychody -->
                    <a href="{{ route('finances.incomes') }}" class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-lg shadow hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-green-100 bg-opacity-10 flex items-center justify-center">
                                <i class="fas fa-plus text-green-600 text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Przychody</h3>
                                <p class="text-sm text-gray-600">Zarządzanie przychodami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Koszta -->
                    <a href="{{ route('finances.expenses') }}" class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-lg shadow hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-red-100 bg-opacity-10 flex items-center justify-center">
                                <i class="fas fa-minus text-red-600 text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Koszta</h3>
                                <p class="text-sm text-gray-600">Zarządzanie kosztami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Faktury -->
                    <a href="{{ route('finances.invoices.index') }}" class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-lg shadow hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-blue-100 bg-opacity-10 flex items-center justify-center">
                                <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Faktury</h3>
                                <p class="text-sm text-gray-600">Zarządzanie fakturami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Księgowość -->
                    <a href="{{ route('finances.accounting') }}" class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-lg shadow hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-purple-100 bg-opacity-10 flex items-center justify-center">
                                <i class="fas fa-book text-purple-600 text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Księgowość</h3>
                                <p class="text-sm text-gray-600">Zarządzanie księgowością</p>
                            </div>
                        </div>
                    </a>

                    <!-- Raporty -->
                    <a href="{{ route('finances.reports') }}" class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-lg shadow hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-indigo-100 bg-opacity-10 flex items-center justify-center">
                                <i class="fas fa-chart-pie text-indigo-600 text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Raporty</h3>
                                <p class="text-sm text-gray-600">Analizy i zestawienia</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 