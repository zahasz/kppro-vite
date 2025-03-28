<x-admin-layout>
    <x-slot name="header">
        Statystyki przychodów
    </x-slot>

    <div class="space-y-6">
        <!-- Panel informacyjny przychodów -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Przychody z subskrypcji</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Dziś</p>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format($revenueStats['total_today'], 2) }} zł</p>
                            @if (is_numeric(str_replace(['+', '-', '%'], '', $revenueStats['compared_yesterday'])))
                                @if (str_starts_with($revenueStats['compared_yesterday'], '+'))
                                    <p class="text-xs text-emerald-500">{{ $revenueStats['compared_yesterday'] }}</p>
                                @elseif (str_starts_with($revenueStats['compared_yesterday'], '-'))
                                    <p class="text-xs text-red-500">{{ $revenueStats['compared_yesterday'] }}</p>
                                @else
                                    <p class="text-xs text-gray-500">{{ $revenueStats['compared_yesterday'] }}</p>
                                @endif
                            @else
                                <p class="text-xs text-gray-500">{{ $revenueStats['compared_yesterday'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Ten miesiąc</p>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format($revenueStats['total_month'], 2) }} zł</p>
                            @if (is_numeric(str_replace(['+', '-', '%'], '', $revenueStats['compared_last_month'])))
                                @if (str_starts_with($revenueStats['compared_last_month'], '+'))
                                    <p class="text-xs text-emerald-500">{{ $revenueStats['compared_last_month'] }}</p>
                                @elseif (str_starts_with($revenueStats['compared_last_month'], '-'))
                                    <p class="text-xs text-red-500">{{ $revenueStats['compared_last_month'] }}</p>
                                @else
                                    <p class="text-xs text-gray-500">{{ $revenueStats['compared_last_month'] }}</p>
                                @endif
                            @else
                                <p class="text-xs text-gray-500">{{ $revenueStats['compared_last_month'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Ten rok</p>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format($revenueStats['total_year'], 2) }} zł</p>
                            @if (is_numeric(str_replace(['+', '-', '%'], '', $revenueStats['compared_last_year'])))
                                @if (str_starts_with($revenueStats['compared_last_year'], '+'))
                                    <p class="text-xs text-emerald-500">{{ $revenueStats['compared_last_year'] }}</p>
                                @elseif (str_starts_with($revenueStats['compared_last_year'], '-'))
                                    <p class="text-xs text-red-500">{{ $revenueStats['compared_last_year'] }}</p>
                                @else
                                    <p class="text-xs text-gray-500">{{ $revenueStats['compared_last_year'] }}</p>
                                @endif
                            @else
                                <p class="text-xs text-gray-500">{{ $revenueStats['compared_last_year'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wykres przychodów -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Wykres przychodów (miesięczny)</h2>
            </div>
            <div class="p-4">
                <div class="h-80 w-full bg-gradient-to-b from-steel-blue-50 to-white rounded border border-gray-200 flex items-center justify-center">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Źródła przychodów -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Źródła przychodów</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Plan Basic</h3>
                            <span class="text-xs bg-steel-blue-100 text-steel-blue-800 py-1 px-2 rounded-full">35%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-steel-blue-600 h-2.5 rounded-full" style="width: 35%"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">10 080 zł</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Plan Premium</h3>
                            <span class="text-xs bg-steel-blue-100 text-steel-blue-800 py-1 px-2 rounded-full">45%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-steel-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">12 960 zł</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Plan Business</h3>
                            <span class="text-xs bg-steel-blue-100 text-steel-blue-800 py-1 px-2 rounded-full">20%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-steel-blue-600 h-2.5 rounded-full" style="width: 20%"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">5 760 zł</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Typy subskrypcji -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Porównanie typów subskrypcji</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Subskrypcje ręczne</h3>
                            <span class="text-xs bg-orange-100 text-orange-800 py-1 px-2 rounded-full">{{ $revenueStats['manual_percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-orange-600 h-2.5 rounded-full" style="width: {{ $revenueStats['manual_percentage'] ?? 0 }}%"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">{{ number_format($revenueStats['manual_value'] ?? 0, 2) }} zł</p>
                        <p class="text-xs text-gray-500 mt-1">Liczba: {{ $revenueStats['manual_count'] ?? 0 }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Subskrypcje automatyczne</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 py-1 px-2 rounded-full">{{ $revenueStats['automatic_percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $revenueStats['automatic_percentage'] ?? 0 }}%"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">{{ number_format($revenueStats['automatic_value'] ?? 0, 2) }} zł</p>
                        <p class="text-xs text-gray-500 mt-1">Liczba: {{ $revenueStats['automatic_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            const revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['months']),
                    datasets: [{
                        label: 'Przychody miesięczne (PLN)',
                        data: @json($chartData['values']),
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
        });
    </script>
    @endpush
</x-admin-layout> 