<x-admin-layout>
    <x-slot name="header">
        Historia płatności
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Historia płatności</h2>
            
            <div class="flex">
                <div class="relative mr-4">
                    <input type="text" name="search" id="search" placeholder="Szukaj płatności..." class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                
                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Eksportuj CSV
                </button>
            </div>
        </div>

        <div class="p-4 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-full">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-green-800">Przychód miesięczny</h2>
                            <div class="text-lg font-semibold text-green-900">24,786 PLN</div>
                            <div class="text-xs text-green-700">
                                <span class="font-medium">+12.5%</span> względem poprzedniego miesiąca
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-blue-800">Liczba płatności</h2>
                            <div class="text-lg font-semibold text-blue-900">317</div>
                            <div class="text-xs text-blue-700">
                                <span class="font-medium">+8.2%</span> względem poprzedniego miesiąca
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-full">
                            <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-purple-800">Średnia wartość</h2>
                            <div class="text-lg font-semibold text-purple-900">78.19 PLN</div>
                            <div class="text-xs text-purple-700">
                                <span class="font-medium">+3.5%</span> względem poprzedniego miesiąca
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-2 rounded-full">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-red-800">Nieudane płatności</h2>
                            <div class="text-lg font-semibold text-red-900">14</div>
                            <div class="text-xs text-red-700">
                                <span class="font-medium">-2.1%</span> względem poprzedniego miesiąca
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700">Status płatności</label>
                        <select id="payment_status" name="payment_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="all" selected>Wszystkie statusy</option>
                            <option value="successful">Opłacone</option>
                            <option value="pending">Oczekujące</option>
                            <option value="failed">Nieudane</option>
                            <option value="refunded">Zwrócone</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                        <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="all" selected>Wszystkie metody</option>
                            <option value="card">Karta płatnicza</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Przelew bankowy</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Od daty</label>
                        <input type="date" name="date_from" id="date_from" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Do daty</label>
                        <input type="date" name="date_to" id="date_to" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div class="pt-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Filtruj
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transakcji</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownik</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kwota</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metoda</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Przykładowe dane, docelowo powinny być pobierane z bazy -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TXN8765432190</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15.10.2023 14:23</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">Jan Kowalski</div>
                                <div class="text-sm text-gray-500 ml-1">(jan.kowalski@example.com)</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Premium (Miesięcznie)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">99 PLN</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Opłacona
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Karta (Visa ****4512)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.payment-details', 'TXN8765432190') }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <button class="text-gray-600 hover:text-gray-900">Faktura</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TXN8765432189</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15.10.2023 12:05</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">Anna Nowak</div>
                                <div class="text-sm text-gray-500 ml-1">(anna.nowak@example.com)</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Standard (Miesięcznie)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">49 PLN</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Opłacona
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">PayPal</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.payment-details', 'TXN8765432189') }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <button class="text-gray-600 hover:text-gray-900">Faktura</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TXN8765432188</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15.10.2023 10:41</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">Tomasz Kaczmarek</div>
                                <div class="text-sm text-gray-500 ml-1">(tomasz.kaczmarek@example.com)</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Premium (Miesięcznie)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">99 PLN</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Oczekująca
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Karta (Mastercard ****7890)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.payment-details', 'TXN8765432188') }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <button class="text-gray-600 hover:text-gray-900">Faktura</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TXN8765432187</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14.10.2023 23:15</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">Piotr Wiśniewski</div>
                                <div class="text-sm text-gray-500 ml-1">(piotr.wisniewski@example.com)</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Premium Roczny</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">999 PLN</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Opłacona
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Przelew bankowy</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.payment-details', 'TXN8765432187') }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <button class="text-gray-600 hover:text-gray-900">Faktura</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TXN8765432186</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14.10.2023 18:22</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">Katarzyna Lis</div>
                                <div class="text-sm text-gray-500 ml-1">(k.lis@example.com)</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Standard (Miesięcznie)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">49 PLN</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Nieudana
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Karta (Visa ****6214)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.payment-details', 'TXN8765432186') }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <button class="text-gray-600 hover:text-gray-900">Faktura</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Poprzednia
                    </a>
                    <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Następna
                    </a>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">1</span>
                            to
                            <span class="font-medium">5</span>
                            of
                            <span class="font-medium">317</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Poprzednia</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                1
                            </a>
                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                2
                            </a>
                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                3
                            </a>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                ...
                            </span>
                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                64
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Następna</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 