<x-admin-layout>
    <x-slot name="header">
        Edycja subskrypcji użytkownika
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edycja subskrypcji użytkownika</h2>
                    
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700">
                                    Edytujesz subskrypcję dla użytkownika <span class="font-bold">Jan Kowalski</span> (jan.kowalski@example.com)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.subscriptions.update-user-subscription', 1) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="subscription_plan" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select id="subscription_plan" name="subscription_plan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="free">Darmowy (0 PLN)</option>
                                    <option value="standard">Standard (49 PLN / miesiąc)</option>
                                    <option value="premium" selected>Premium (99 PLN / miesiąc)</option>
                                    <option value="premium_yearly">Premium Roczny (999 PLN / rok)</option>
                                </select>
                                @error('subscription_plan')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="active" selected>Aktywna</option>
                                    <option value="pending">Oczekująca</option>
                                    <option value="cancelled">Anulowana</option>
                                    <option value="expired">Wygasła</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                                <input type="date" name="start_date" id="start_date" value="2023-01-15" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
                                <input type="date" name="end_date" id="end_date" value="2023-12-15" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla bezterminowej subskrypcji</p>
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="card" selected>Karta płatnicza</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Przelew bankowy</option>
                                    <option value="none">Brak (darmowy plan)</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_details" class="block text-sm font-medium text-gray-700">Szczegóły płatności</label>
                                <input type="text" name="payment_details" id="payment_details" value="Visa ****4512" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">np. ostatnie 4 cyfry karty, adres email PayPal</p>
                                @error('payment_details')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Historia płatności</h3>
                            <div class="mt-4 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Data</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kwota</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Metoda płatności</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nr transakcji</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">15.10.2023</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">99 PLN</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Opłacona
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Visa ****4512</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">TXN8765432190</td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">15.09.2023</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">99 PLN</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Opłacona
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Visa ****4512</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">TXN8765432189</td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">15.08.2023</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">99 PLN</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Opłacona
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Visa ****4512</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">TXN8765432188</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex justify-between">
                                <div>
                                    <button type="button" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Anuluj subskrypcję
                                    </button>
                                </div>
                                <div class="flex">
                                    <a href="{{ route('admin.subscriptions.users') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                        Anuluj
                                    </a>
                                    <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Zapisz zmiany
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 