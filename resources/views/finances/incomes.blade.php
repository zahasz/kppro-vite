@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Przychody') }}</h2>
            <div class="flex items-center space-x-2">
                <button class="btn flex items-center space-x-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-gray-700 px-4 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-print"></i>
                    <span class="text-sm">Drukuj</span>
                </button>
                <button class="btn flex items-center space-x-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-gray-700 px-4 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-file-pdf"></i>
                    <span class="text-sm">PDF</span>
                </button>
                <button class="btn flex items-center space-x-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    <span class="text-sm">Dodaj przychód</span>
                </button>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Menu boczne -->
            @include('layouts.sidebar')

            <!-- Główna zawartość -->
            <div class="flex-1">
                <!-- Statystyki -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Przychód miesięczny -->
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

                    <!-- Przychód roczny -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-green-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Przychód roczny</h4>
                            <p class="text-xl font-bold text-green-600">145 800 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 bg-opacity-10" style="width: 85%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">85% celu rocznego</p>
                        </div>
                    </div>

                    <!-- Średni przychód -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-md relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500/40"></div>
                        <div class="absolute right-0 top-0 text-4xl text-blue-500/40 transform -translate-x-[20px] translate-y-[20px]">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="p-3 relative z-10">
                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 whitespace-nowrap">Średni przychód</h4>
                            <p class="text-xl font-bold text-blue-600">12 150 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 bg-opacity-10" style="width: 65%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Miesięcznie</p>
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
                            <p class="text-xl font-bold text-amber-600">3 800 zł</p>
                            <div class="mt-1.5 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 bg-opacity-10" style="width: 30%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Do rozliczenia</p>
                        </div>
                    </div>
                </div>

                <!-- Lista przychodów -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white bg-opacity-10">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                        Data
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                        Kategoria
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                        Opis
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                        Kwota
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Akcje</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                        2024-02-22
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                        Sprzedaż usług
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-700">
                                                        Projekt strony internetowej
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                                        5 000,00 zł
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Opłacone
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button class="text-blue-600 hover:text-blue-900 mr-2">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <!-- Więcej wierszy... -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 