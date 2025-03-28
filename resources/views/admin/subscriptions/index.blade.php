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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Ręczna
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Automatyczna
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Automatyczna
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Automatyczna
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
            
            <div class="flex flex-wrap mt-4 gap-2">
                <button class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Wszystkie
                </button>
                <button class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ręczne
                </button>
                <button class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Automatyczne
                </button>
                <button class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Aktywne
                </button>
                <button class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Nieaktywne
                </button>
            </div>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Aktywne subskrypcje</h3>
                <div class="text-3xl font-bold text-indigo-600">{{ $stats['active_subscriptions'] ?? 0 }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    @if(isset($stats['sub_monthly_change']))
                        @if($stats['sub_monthly_change'] > 0)
                            <span class="text-green-600">+{{ $stats['sub_monthly_change'] }}%</span>
                        @elseif($stats['sub_monthly_change'] < 0)
                            <span class="text-red-600">{{ $stats['sub_monthly_change'] }}%</span>
                        @else
                            <span>0%</span>
                        @endif
                        w porównaniu do poprzedniego miesiąca
                    @else
                        Brak danych porównawczych
                    @endif
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Przychód miesięczny</h3>
                <div class="text-3xl font-bold text-green-600">{{ number_format($stats['month_subscriptions_value'] ?? 0, 2) }} PLN</div>
                <div class="text-sm text-gray-500 mt-1">
                    @if(isset($stats['revenue_monthly_change']))
                        @if($stats['revenue_monthly_change'] > 0)
                            <span class="text-green-600">+{{ $stats['revenue_monthly_change'] }}%</span>
                        @elseif($stats['revenue_monthly_change'] < 0)
                            <span class="text-red-600">{{ $stats['revenue_monthly_change'] }}%</span>
                        @else
                            <span>0%</span>
                        @endif
                        w porównaniu do poprzedniego miesiąca
                    @else
                        Brak danych porównawczych
                    @endif
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Średnia wartość subskrypcji</h3>
                <div class="text-3xl font-bold text-blue-600">
                    @if(isset($stats['active_subscriptions']) && $stats['active_subscriptions'] > 0)
                        {{ number_format(($stats['active_subscriptions_value'] ?? 0) / $stats['active_subscriptions'], 2) }} PLN
                    @else
                        0.00 PLN
                    @endif
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    @if(isset($stats['avg_value_monthly_change']))
                        @if($stats['avg_value_monthly_change'] > 0)
                            <span class="text-green-600">+{{ $stats['avg_value_monthly_change'] }}%</span>
                        @elseif($stats['avg_value_monthly_change'] < 0)
                            <span class="text-red-600">{{ $stats['avg_value_monthly_change'] }}%</span>
                        @else
                            <span>0%</span>
                        @endif
                        w porównaniu do poprzedniego miesiąca
                    @else
                        Brak danych porównawczych
                    @endif
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Subskrypcje ręczne</h3>
                <div class="text-3xl font-bold text-orange-600">{{ $stats['manual_subscriptions'] ?? 0 }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $stats['manual_percentage'] ?? 0 }}% wszystkich subskrypcji
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Subskrypcje automatyczne</h3>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['automatic_subscriptions'] ?? 0 }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $stats['automatic_percentage'] ?? 0 }}% wszystkich subskrypcji
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 