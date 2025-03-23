<x-admin-layout>
    <x-slot name="header">
        Zarządzanie planami subskrypcji
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Plany subskrypcji</h2>
            <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Dodaj nowy plan
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa planu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Okres</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownicy</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Przykładowe dane, docelowo powinny być pobierane z bazy -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Darmowy</div>
                                    <div class="text-sm text-gray-500">Podstawowe funkcje</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">0 PLN</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Bezterminowo</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            245
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.edit', 1) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                            <button class="text-red-600 hover:text-red-900">Ukryj</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Standard</div>
                                    <div class="text-sm text-gray-500">Wszystkie podstawowe funkcje</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">49 PLN</div>
                            <div class="text-sm text-gray-500">miesięcznie</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Miesięczny</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            118
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.edit', 2) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                            <button class="text-red-600 hover:text-red-900">Ukryj</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Premium</div>
                                    <div class="text-sm text-gray-500">Pełny dostęp do wszystkich funkcji</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">99 PLN</div>
                            <div class="text-sm text-gray-500">miesięcznie</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Miesięczny</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            76
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.edit', 3) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                            <button class="text-red-600 hover:text-red-900">Ukryj</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Premium Roczny</div>
                                    <div class="text-sm text-gray-500">Pełny dostęp, płatność roczna</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">999 PLN</div>
                            <div class="text-sm text-gray-500">rocznie</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Roczny</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            42
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscriptions.edit', 4) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                            <button class="text-red-600 hover:text-red-900">Ukryj</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Statystyki subskrypcji</h2>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Aktywne subskrypcje</h3>
                <div class="text-3xl font-bold text-indigo-600">481</div>
                <div class="text-sm text-gray-500 mt-1">+12% w porównaniu do poprzedniego miesiąca</div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Przychód miesięczny</h3>
                <div class="text-3xl font-bold text-green-600">24 786 PLN</div>
                <div class="text-sm text-gray-500 mt-1">+8% w porównaniu do poprzedniego miesiąca</div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Średnia wartość subskrypcji</h3>
                <div class="text-3xl font-bold text-blue-600">78,21 PLN</div>
                <div class="text-sm text-gray-500 mt-1">+2% w porównaniu do poprzedniego miesiąca</div>
            </div>
        </div>
    </div>
</x-admin-layout> 