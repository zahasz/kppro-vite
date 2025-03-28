<x-admin-layout>
    <x-slot name="header">
        Raporty miesięczne przychodów
    </x-slot>

    <div class="space-y-6">
        <!-- Filtry -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Miesiąc</label>
                        <select id="month" name="month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-steel-blue-500 focus:border-steel-blue-500 sm:text-sm rounded-md">
                            <option value="01" {{ request('month') == '01' ? 'selected' : '' }}>Styczeń</option>
                            <option value="02" {{ request('month') == '02' ? 'selected' : '' }}>Luty</option>
                            <option value="03" {{ request('month') == '03' ? 'selected' : '' }}>Marzec</option>
                            <option value="04" {{ request('month') == '04' ? 'selected' : '' }}>Kwiecień</option>
                            <option value="05" {{ request('month') == '05' ? 'selected' : '' }}>Maj</option>
                            <option value="06" {{ request('month') == '06' ? 'selected' : '' }}>Czerwiec</option>
                            <option value="07" {{ request('month') == '07' ? 'selected' : '' }}>Lipiec</option>
                            <option value="08" {{ request('month') == '08' ? 'selected' : '' }}>Sierpień</option>
                            <option value="09" {{ request('month') == '09' ? 'selected' : '' }}>Wrzesień</option>
                            <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Październik</option>
                            <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>Listopad</option>
                            <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Grudzień</option>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Rok</label>
                        <select id="year" name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-steel-blue-500 focus:border-steel-blue-500 sm:text-sm rounded-md">
                            <option value="2023" {{ request('year') == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                        </select>
                    </div>
                    <div>
                        <label for="plan" class="block text-sm font-medium text-gray-700">Plan</label>
                        <select id="plan" name="plan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-steel-blue-500 focus:border-steel-blue-500 sm:text-sm rounded-md">
                            <option value="">Wszystkie</option>
                            <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ request('plan') == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="business" {{ request('plan') == 'business' ? 'selected' : '' }}>Business</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-steel-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-steel-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                            Filtruj
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Podsumowanie -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Podsumowanie miesiąca: {{ $monthName }} {{ $year }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Całkowity przychód</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($summary['total'], 2) }} zł</p>
                </div>
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Liczba nowych subskrypcji</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $summary['new_subscriptions'] }}</p>
                </div>
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Średnia wartość subskrypcji</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($summary['average'], 2) }} zł</p>
                </div>
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Zmiana (m/m)</p>
                    <div class="flex items-center">
                        <p class="text-xl font-semibold {{ $summary['change'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $summary['change'] >= 0 ? '+' : '' }}{{ number_format($summary['change'], 2) }}%
                        </p>
                        @if($summary['change'] >= 0)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                        </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wykres dzienny -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Przychody dzienne</h2>
            </div>
            <div class="p-4">
                <div class="h-80 w-full bg-gradient-to-b from-steel-blue-50 to-white rounded border border-gray-200 flex items-center justify-center">
                    <canvas id="dailyRevenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Szczegółowe dane -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Szczegóły transakcji</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Użytkownik
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Plan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kwota
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction['date'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction['user'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-steel-blue-100 text-steel-blue-800">
                                    {{ $transaction['plan'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($transaction['amount'], 2) }} zł
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($transaction['status'] === 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Zrealizowana
                                </span>
                                @elseif($transaction['status'] === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Oczekująca
                                </span>
                                @elseif($transaction['status'] === 'failed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Nieudana
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
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
                            Wyświetlanie <span class="font-medium">1</span> do <span class="font-medium">10</span> z <span class="font-medium">{{ count($transactions) }}</span> wyników
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Poprzednia</span>
                                <!-- Heroicon name: solid/chevron-left -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" aria-current="page" class="z-10 bg-steel-blue-50 border-steel-blue-500 text-steel-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Następna</span>
                                <!-- Heroicon name: solid/chevron-right -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Eksport -->
        <div class="flex justify-end">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-steel-blue-600 hover:bg-steel-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Eksportuj raport (PDF)
            </button>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
            
            const dailyRevenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($dailyChartData['days']),
                    datasets: [{
                        label: 'Dzienne przychody (PLN)',
                        data: @json($dailyChartData['values']),
                        backgroundColor: 'rgba(84, 110, 149, 0.1)',
                        borderColor: 'rgba(84, 110, 149, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout> 