<x-admin-layout>
    <x-slot name="header">
        Statystyki subskrypcji
    </x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Podsumowanie subskrypcji</h2>
            <p class="mt-1 text-sm text-gray-600">Analiza aktywności i przychodów z subskrypcji.</p>
        </div>

        <!-- Podstawowe statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Aktywne subskrypcje</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    @if($stats['subscription_change'] > 0)
                        <p class="text-sm text-green-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            +{{ $stats['subscription_change'] }}% od ostatniego miesiąca
                        </p>
                    @elseif($stats['subscription_change'] < 0)
                        <p class="text-sm text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            {{ $stats['subscription_change'] }}% od ostatniego miesiąca
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Bez zmian</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Przychód miesięczny</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['revenue_this_month'], 2) }} zł</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    @if($stats['revenue_change'] > 0)
                        <p class="text-sm text-green-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            +{{ $stats['revenue_change'] }}% od ostatniego miesiąca
                        </p>
                    @elseif($stats['revenue_change'] < 0)
                        <p class="text-sm text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            {{ $stats['revenue_change'] }}% od ostatniego miesiąca
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Bez zmian</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Średnia wartość subskrypcji</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['avg_sub_value'], 2) }} zł</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Aktywne subskrypcje</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Łącznie subskrypcji</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</p>
                    </div>
                    <div class="p-3 bg-amber-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Wszystkie typy subskrypcji</p>
                </div>
            </div>
        </div>

        <!-- Wizualizacja danych -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Wykres miesięczny -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Przychody miesięczne</h3>
                <div class="h-80">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>

            <!-- Podział subskrypcji -->
            <div class="bg-white rounded-lg shadow-md p-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Typy subskrypcji</h3>
                <div class="h-60">
                    <canvas id="subscriptionTypeChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>Ręczne ({{ $stats['manual_percentage'] }}%)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Automatyczne ({{ $stats['automatic_percentage'] }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plany subskrypcji -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Statystyki planów</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktywne subskrypcje</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Przychód miesięczny</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Udział w przychodzie</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['plan_stats'] as $plan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan['active_count'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($plan['revenue'], 2) }} zł</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $plan['revenue'] > 0 ? ($plan['revenue'] / $stats['active_subscriptions_value']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">{{ $plan['revenue'] > 0 ? number_format(($plan['revenue'] / $stats['active_subscriptions_value']) * 100, 1) : 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dane dla wykresu miesięcznego
            const monthlyData = @json($stats['monthly_stats']);
            
            // Wykres przychodów miesięcznych
            const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            new Chart(monthlyRevenueCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [
                        {
                            label: 'Przychód (zł)',
                            data: monthlyData.map(item => item.revenue),
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Liczba subskrypcji',
                            data: monthlyData.map(item => item.subscriptions),
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Przychód (zł)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Liczba subskrypcji'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Miesiąc'
                            }
                        }
                    }
                }
            });

            // Wykres typów subskrypcji
            const subscriptionTypeCtx = document.getElementById('subscriptionTypeChart').getContext('2d');
            new Chart(subscriptionTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ręczne', 'Automatyczne'],
                    datasets: [{
                        data: [{{ $stats['manual_subscriptions'] }}, {{ $stats['automatic_subscriptions'] }}],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout> 