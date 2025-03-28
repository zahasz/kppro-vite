<x-admin-layout>
    <x-slot name="header">
        Raporty roczne przychodów
    </x-slot>

    <div class="space-y-6">
        <!-- Filtry -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <h2 class="text-base font-medium text-gray-900">Podsumowanie roku {{ $year }}</h2>
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
                    <p class="text-sm font-medium text-gray-500">Zmiana (r/r)</p>
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
        
        <!-- Wykresy roczne -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Wykres przychodów miesięcznych -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-base font-medium text-gray-900">Przychody miesięczne</h2>
                </div>
                <div class="p-4">
                    <div class="h-80 w-full bg-gradient-to-b from-steel-blue-50 to-white rounded border border-gray-200 flex items-center justify-center">
                        <canvas id="monthlyRevenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Wykres planów subskrypcji -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-base font-medium text-gray-900">Podział planów subskrypcji</h2>
                </div>
                <div class="p-4">
                    <div class="h-80 w-full bg-gradient-to-b from-steel-blue-50 to-white rounded border border-gray-200 flex items-center justify-center">
                        <canvas id="planDistributionChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Porównanie kwartałów -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Porównanie kwartałów</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Wskaźnik
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Q1
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Q2
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Q3
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Q4
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Przychód
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ number_format($quarterlyData['revenue']['q1'], 2) }} zł
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ number_format($quarterlyData['revenue']['q2'], 2) }} zł
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ number_format($quarterlyData['revenue']['q3'], 2) }} zł
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ number_format($quarterlyData['revenue']['q4'], 2) }} zł
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Nowe subskrypcje
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['new_subs']['q1'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['new_subs']['q2'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['new_subs']['q3'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['new_subs']['q4'] }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Wznowione subskrypcje
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['renewals']['q1'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['renewals']['q2'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['renewals']['q3'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['renewals']['q4'] }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Anulowane subskrypcje
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['cancellations']['q1'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['cancellations']['q2'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['cancellations']['q3'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $quarterlyData['cancellations']['q4'] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Trendy roczne -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Trendy roczne</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Wzrost przychodów</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-semibold text-gray-900">{{ $yearlyTrends['revenue_growth'] }}%</span>
                            <div class="text-xs px-2 py-1 rounded-full {{ $yearlyTrends['revenue_growth'] >= 10 ? 'bg-green-100 text-green-800' : ($yearlyTrends['revenue_growth'] >= 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $yearlyTrends['revenue_growth'] >= 10 ? 'Doskonały' : ($yearlyTrends['revenue_growth'] >= 0 ? 'Stabilny' : 'Wymagający uwagi') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Retencja klientów</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-semibold text-gray-900">{{ $yearlyTrends['retention_rate'] }}%</span>
                            <div class="text-xs px-2 py-1 rounded-full {{ $yearlyTrends['retention_rate'] >= 80 ? 'bg-green-100 text-green-800' : ($yearlyTrends['retention_rate'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $yearlyTrends['retention_rate'] >= 80 ? 'Doskonały' : ($yearlyTrends['retention_rate'] >= 60 ? 'Dobry' : 'Wymagający uwagi') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Wartość życiowa klienta (CLV)</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-semibold text-gray-900">{{ number_format($yearlyTrends['customer_lifetime_value'], 2) }} zł</span>
                            <div class="text-xs px-2 py-1 rounded-full {{ $yearlyTrends['clv_growth'] >= 5 ? 'bg-green-100 text-green-800' : ($yearlyTrends['clv_growth'] >= 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $yearlyTrends['clv_growth'] >= 0 ? '+' : '' }}{{ $yearlyTrends['clv_growth'] }}% r/r
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Średni miesiąc wzrostu (MoM)</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-semibold text-gray-900">{{ $yearlyTrends['avg_mom_growth'] }}%</span>
                            <div class="text-xs px-2 py-1 rounded-full {{ $yearlyTrends['avg_mom_growth'] >= 5 ? 'bg-green-100 text-green-800' : ($yearlyTrends['avg_mom_growth'] >= 1 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $yearlyTrends['avg_mom_growth'] >= 5 ? 'Wysoki wzrost' : ($yearlyTrends['avg_mom_growth'] >= 1 ? 'Umiarkowany wzrost' : 'Słaby wzrost') }}
                            </div>
                        </div>
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
                Eksportuj raport roczny (PDF)
            </button>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wykres miesięcznych przychodów
            const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            const monthlyRevenueChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru'],
                    datasets: [{
                        label: 'Przychody miesięczne (PLN)',
                        data: @json($chartData['monthly_values']),
                        backgroundColor: 'rgba(84, 110, 149, 0.5)',
                        borderColor: 'rgba(84, 110, 149, 1)',
                        borderWidth: 1
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
            
            // Wykres podziału planów subskrypcji
            const planCtx = document.getElementById('planDistributionChart').getContext('2d');
            const planDistributionChart = new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['plan_labels']),
                    datasets: [{
                        label: 'Podział planów',
                        data: @json($chartData['plan_values']),
                        backgroundColor: [
                            'rgba(84, 110, 149, 0.7)',
                            'rgba(49, 130, 189, 0.7)',
                            'rgba(107, 174, 214, 0.7)',
                        ],
                        borderColor: [
                            'rgba(84, 110, 149, 1)',
                            'rgba(49, 130, 189, 1)',
                            'rgba(107, 174, 214, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout> 