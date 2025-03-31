@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Faktura {{ $invoice->number }}</h1>
            <p class="text-gray-600">Wystawiona dnia {{ $invoice->issue_date->format('d.m.Y') }}</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.billing.invoices') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Powrót do listy
            </a>
            <a href="{{ route('admin.billing.invoices.pdf', $invoice->id) }}" class="inline-block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md shadow-sm">
                <i class="fas fa-file-pdf mr-2"></i> Pobierz PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-medium mb-4">Informacje o fakturze</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-500">Numer faktury:</span>
                            <span class="font-medium">{{ $invoice->number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Data wystawienia:</span>
                            <span>{{ $invoice->issue_date->format('d.m.Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Data sprzedaży:</span>
                            <span>{{ $invoice->sale_date->format('d.m.Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Termin płatności:</span>
                            <span>{{ $invoice->due_date->format('d.m.Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Status:</span>
                            @if($invoice->status == 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Opłacona
                                </span>
                            @elseif($invoice->status == 'issued')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Wystawiona
                                </span>
                            @elseif($invoice->status == 'overdue')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Zaległa
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500">Metoda płatności:</span>
                            <span>{{ $invoice->payment_method ?? 'Nie określono' }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-medium mb-4">Dane klienta</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-500">Użytkownik:</span>
                            <span class="font-medium">{{ $invoice->user->name ?? 'Brak' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <span>{{ $invoice->user->email ?? 'Brak' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Nazwa firmy:</span>
                            <span>{{ $invoice->contractor_name ?? 'Brak' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">NIP:</span>
                            <span>{{ $invoice->contractor_nip ?? 'Brak' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Adres:</span>
                            <span>{{ $invoice->contractor_address ?? 'Brak' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h2 class="text-lg font-medium mb-4">Pozycje faktury</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lp.
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nazwa
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ilość
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cena jedn. netto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Wartość netto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stawka VAT
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kwota VAT
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Wartość brutto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoice->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 2) }} {{ $invoice->currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->net_price, 2) }} {{ $invoice->currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tax_rate }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->tax_amount, 2) }} {{ $invoice->currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->gross_price, 2) }} {{ $invoice->currency }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Brak pozycji na fakturze.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-medium">
                                Razem:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{ number_format($invoice->net_total, 2) }} {{ $invoice->currency }}
                            </td>
                            <td></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{ number_format($invoice->tax_total, 2) }} {{ $invoice->currency }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{ number_format($invoice->gross_total, 2) }} {{ $invoice->currency }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->notes)
            <div class="p-6 border-t border-gray-200">
                <h2 class="text-lg font-medium mb-2">Uwagi</h2>
                <p class="text-gray-700">{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection 