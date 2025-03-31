@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Statystyki faktur</h1>
            
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                            <i class="fas fa-home mr-2"></i>
                            Panel
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                            <a href="{{ route('admin.billing.invoices') }}" class="text-gray-700 hover:text-blue-600">
                                Faktury
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                            <span class="text-gray-500">Statystyki</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Filtry -->
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <form action="{{ route('admin.billing.statistics') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700">Okres:</label>
                    <select id="period" name="period" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                        <option value="month" {{ request('period') == 'month' || !request('period') ? 'selected' : '' }}>Ostatni miesiąc</option>
                        <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Ostatni kwartał</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Ostatni rok</option>
                        <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Wszystkie</option>
                    </select>
                </div>
                
                <div class="flex-1"></div>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                    Zastosuj filtry
                </button>
            </form>
        </div>

        <!-- Karty ze statystykami -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <!-- Łączna wartość faktur -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Łączna wartość faktur
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($stats['total_value'], 2, ',', ' ') }} {{ $settings->default_currency }}
                    </dd>
                    <div class="mt-3 flex items-center text-sm">
                        <span class="flex items-center {{ $stats['total_growth'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            @if($stats['total_growth'] >= 0)
                                <i class="fas fa-arrow-up mr-1.5 flex-shrink-0"></i>
                            @else
                                <i class="fas fa-arrow-down mr-1.5 flex-shrink-0"></i>
                            @endif
                            {{ abs($stats['total_growth']) }}%
                        </span>
                        <span class="ml-2 text-gray-500">w porównaniu do poprzedniego okresu</span>
                    </div>
                </div>
            </div>

            <!-- Liczba faktur -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Liczba wystawionych faktur
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $stats['invoices_count'] }}
                    </dd>
                    <div class="mt-3 flex items-center text-sm">
                        <span class="flex items-center {{ $stats['count_growth'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            @if($stats['count_growth'] >= 0)
                                <i class="fas fa-arrow-up mr-1.5 flex-shrink-0"></i>
                            @else
                                <i class="fas fa-arrow-down mr-1.5 flex-shrink-0"></i>
                            @endif
                            {{ abs($stats['count_growth']) }}%
                        </span>
                        <span class="ml-2 text-gray-500">w porównaniu do poprzedniego okresu</span>
                    </div>
                </div>
            </div>

            <!-- Opłacone faktury -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Opłacone faktury
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $stats['paid_count'] }} ({{ $stats['paid_percentage'] }}%)
                    </dd>
                    <div class="mt-3 flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $stats['paid_percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zaległe faktury -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Zaległe faktury
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $stats['overdue_count'] }} ({{ $stats['overdue_percentage'] }}%)
                    </dd>
                    <div class="mt-3 flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $stats['overdue_percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wykresy -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Wykres przychodu miesięcznego -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Miesięczne przychody</h2>
                <div class="relative">
                    <canvas id="monthlyRevenueChart" height="200"></canvas>
                </div>
            </div>

            <!-- Wykres statusów faktur -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Status faktur</h2>
                <div class="relative">
                    <canvas id="invoiceStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Ostatnie faktury -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ostatnio wystawione faktury</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Numer faktury
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Klient
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data wystawienia
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Wartość
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($latestInvoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.billing.invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $invoice->number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $invoice->contractor_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $invoice->issue_date->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ number_format($invoice->gross_total, 2, ',', ' ') }} {{ $invoice->currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($invoice->status == 'paid')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Opłacona
                                        </span>
                                    @elseif($invoice->status == 'overdue')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Zaległa
                                        </span>
                                    @elseif($invoice->status == 'issued')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Wystawiona
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                <a href="{{ route('admin.billing.invoices') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                    Zobacz wszystkie faktury <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Miesięczne przychody
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($charts['monthly_revenue']['labels']) !!},
                datasets: [{
                    label: 'Przychód',
                    data: {!! json_encode($charts['monthly_revenue']['values']) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('pl-PL') + ' {{ $settings->default_currency }}';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       context.parsed.y.toLocaleString('pl-PL') + ' {{ $settings->default_currency }}';
                            }
                        }
                    }
                }
            }
        });

        // Status faktur
        const invoiceStatusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
        const invoiceStatusChart = new Chart(invoiceStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($charts['invoice_status']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($charts['invoice_status']['values']) !!},
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(161, 161, 170, 0.7)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(161, 161, 170)'
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
@endsection 