@extends('layouts.app')

@section('title', 'Szczegóły faktury')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Faktura {{ $invoice->number }}</h2>
        <div class="flex space-x-4">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Powrót do listy
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i>
                Pobierz PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Nagłówek faktury -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- Dane sprzedawcy -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Sprzedawca</h2>
                <div class="space-y-2">
                    <p class="font-medium">{{ $invoice->company->name }}</p>
                    <p>{{ $invoice->company->address }}</p>
                    <p>{{ $invoice->company->postal_code }} {{ $invoice->company->city }}</p>
                    <p>NIP: {{ $invoice->company->tax_number }}</p>
                </div>
            </div>

            <!-- Dane nabywcy -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Nabywca</h2>
                <div class="space-y-2">
                    <p class="font-medium">{{ $invoice->contractor->name }}</p>
                    <p>{{ $invoice->contractor->address }}</p>
                    <p>{{ $invoice->contractor->postal_code }} {{ $invoice->contractor->city }}</p>
                    <p>NIP: {{ $invoice->contractor->tax_number }}</p>
                </div>
            </div>
        </div>

        <!-- Informacje o fakturze -->
        <div class="grid grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Daty</h3>
                <div class="space-y-2">
                    <p><span class="text-gray-600">Data wystawienia:</span> {{ $invoice->issue_date->format('d.m.Y') }}</p>
                    <p><span class="text-gray-600">Data sprzedaży:</span> {{ $invoice->sale_date->format('d.m.Y') }}</p>
                    <p><span class="text-gray-600">Termin płatności:</span> {{ $invoice->due_date->format('d.m.Y') }}</p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Płatność</h3>
                <div class="space-y-2">
                    <p><span class="text-gray-600">Metoda:</span> {{ $invoice->payment_method }}</p>
                    <p><span class="text-gray-600">Status:</span> 
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                   bg-{{ $invoice->payment_status_color }}-100 text-{{ $invoice->payment_status_color }}-800">
                            {{ $invoice->payment_status }}
                        </span>
                    </p>
                    @if($invoice->paid_at)
                        <p><span class="text-gray-600">Data zapłaty:</span> {{ $invoice->paid_at->format('d.m.Y') }}</p>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Status faktury</h3>
                <div class="space-y-2">
                    <p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                   bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                            {{ $invoice->status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pozycje faktury -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pozycje faktury</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lp.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ilość</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">J.m.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cena netto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Wartość netto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">VAT %</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Wartość VAT</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Wartość brutto</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->unit }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($item->net_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($item->tax_amount, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($item->gross_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Razem:</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format($invoice->net_total, 2) }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format($invoice->tax_total, 2) }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format($invoice->gross_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Podsumowanie i uwagi -->
        <div class="grid grid-cols-2 gap-8">
            <div>
                @if($invoice->notes)
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Uwagi</h3>
                    <p class="text-sm text-gray-900">{{ $invoice->notes }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Do zapłaty</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($invoice->gross_total, 2) }} {{ $invoice->currency }}
                </p>
                @if($invoice->bank_account)
                    <div class="mt-4 space-y-1">
                        <p class="text-sm text-gray-600">{{ $invoice->bank_name }}</p>
                        <p class="text-sm font-medium">{{ $invoice->bank_account }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .container, .container * {
        visibility: visible;
    }
    .container {
        position: absolute;
        left: 0;
        top: 0;
    }
    .no-print {
        display: none;
    }
}
</style>
@endpush
@endsection 