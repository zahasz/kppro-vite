@extends('layouts.app')

@section('title', 'Faktury')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Przyciski akcji -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Faktury</h1>
        <div class="space-x-2">
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Nowa faktura
            </a>
            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-file-invoice mr-2"></i>
                Proforma
            </a>
        </div>
    </div>

    <!-- Kafelki statystyk -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Faktury wystawione w tym miesiącu -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                    <i class="fas fa-file-invoice text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Faktury wystawione (ten miesiąc)</h2>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-800">{{ number_format($statistics['this_month']['total']) }}</p>
                        <p class="ml-2 text-sm text-gray-600">
                            {{ number_format($statistics['this_month']['amount'], 2) }} PLN
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faktury niezapłacone -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 bg-opacity-75">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Faktury należne</h2>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-800">{{ number_format($statistics['unpaid']['total']) }}</p>
                        <p class="ml-2 text-sm text-gray-600">
                            {{ number_format($statistics['unpaid']['amount'], 2) }} PLN
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faktury przeterminowane -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 bg-opacity-75">
                    <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Faktury przeterminowane</h2>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-800">{{ number_format($statistics['overdue']['total']) }}</p>
                        <p class="ml-2 text-sm text-gray-600">
                            {{ number_format($statistics['overdue']['amount'], 2) }} PLN
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista faktur -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Numer
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kontrahent
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data wystawienia
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Termin płatności
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kwota brutto
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Akcje
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $invoice->number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->contractor_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->issue_date->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->due_date->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($invoice->gross_total, 2) }} PLN
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                       bg-{{ $invoice->payment_status_color }}-100 text-{{ $invoice->payment_status_color }}-800">
                                {{ $invoice->payment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($invoice->status === 'draft')
                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('Czy na pewno chcesz usunąć tę fakturę?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Brak faktur do wyświetlenia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection 