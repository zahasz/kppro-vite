@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-4">Finanse</h2>
                <p class="text-gray-600 mb-8">Przegląd finansów Twojej firmy</p>

                <!-- Statystyki -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Przychody -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">PRZYCHODY</p>
                                <p class="text-2xl font-semibold text-green-600">{{ number_format($totalIncome ?? 0, 2, ',', ' ') }} zł</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fas fa-plus text-green-600"></i>
                            </span>
                        </div>
                        <p class="text-sm text-green-600 mt-2">
                            <i class="fas fa-arrow-up"></i> 100% wzrost
                        </p>
                    </div>

                    <!-- Wydatki -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">WYDATKI</p>
                                <p class="text-2xl font-semibold text-red-600">{{ number_format($totalExpenses ?? 0, 2, ',', ' ') }} zł</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                                <i class="fas fa-minus text-red-600"></i>
                            </span>
                        </div>
                        <p class="text-sm text-red-600 mt-2">
                            <i class="fas fa-arrow-down"></i> 0% spadek
                        </p>
                    </div>

                    <!-- Zysk -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">ZYSK</p>
                                <p class="text-2xl font-semibold text-blue-600">{{ number_format($profit ?? 0, 2, ',', ' ') }} zł</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600"></i>
                            </span>
                        </div>
                        <p class="text-sm text-blue-600 mt-2">
                            <i class="fas fa-arrow-up"></i> 100% wzrost
                        </p>
                    </div>

                    <!-- Należności -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">NALEŻNOŚCI</p>
                                <p class="text-2xl font-semibold text-orange-600">{{ number_format($receivables ?? 9840, 2, ',', ' ') }} zł</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-file-invoice text-orange-600"></i>
                            </span>
                        </div>
                        <p class="text-sm text-orange-600 mt-2">4 nieopłacone faktury</p>
                    </div>
                </div>

                <!-- Kafelki nawigacyjne -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Przychody -->
                    <a href="{{ route('finances.incomes') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fas fa-plus text-green-600"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">Przychody</h3>
                                <p class="text-gray-600">Zarządzanie przychodami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Koszta -->
                    <a href="{{ route('finances.expenses') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                                <i class="fas fa-minus text-red-600"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">Koszta</h3>
                                <p class="text-gray-600">Zarządzanie kosztami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Faktury -->
                    <a href="{{ route('finances.invoices.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-file-invoice text-blue-600"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">Faktury</h3>
                                <p class="text-gray-600">Zarządzanie fakturami</p>
                            </div>
                        </div>
                    </a>

                    <!-- Księgowość -->
                    <a href="{{ route('finances.accounting') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-book text-purple-600"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">Księgowość</h3>
                                <p class="text-gray-600">Zarządzanie księgowością</p>
                            </div>
                        </div>
                    </a>

                    <!-- Raporty -->
                    <a href="{{ route('finances.reports') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-chart-pie text-indigo-600"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">Raporty</h3>
                                <p class="text-gray-600">Analizy i zestawienia</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 