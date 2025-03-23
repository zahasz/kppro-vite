@extends('layouts.app')

@section('title', 'Edycja faktury')

@push('styles')
<style>
    /* Style dla niestandardowego selecta */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    .custom-select {
        appearance: none;
        width: 100%;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: 1px solid rgb(209, 213, 219);
        background-color: white;
        cursor: pointer;
    }
    .custom-select-wrapper::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .custom-select:focus {
        outline: none;
        border-color: rgb(59, 130, 246);
        box-shadow: 0 0 0 1px rgb(59, 130, 246);
    }
    
    /* Style dla tabeli pozycji faktury */
    .table-select {
        appearance: none;
        width: 100%;
        padding: 0.4rem 0.7rem;
        border-radius: 0.375rem;
        border: 1px solid rgb(209, 213, 219);
        background-color: white;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edycja faktury {{ $invoice->number }}</h1>
        <div class="space-x-2">
            <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Powrót
            </a>
        </div>
    </div>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
        @csrf
        @method('PUT')
        
        <!-- Dane podstawowe -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Dane podstawowe</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700">Numer faktury</label>
                        <input type="text" name="number" id="number" value="{{ $invoice->number }}" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                    </div>

                    <div>
                        <label for="contractor_id" class="block text-sm font-medium text-gray-700">Kontrahent</label>
                        <div class="custom-select-wrapper">
                            <select name="contractor_id" id="contractor_id" required
                                    class="custom-select">
                                <option value="">Wybierz kontrahenta</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor->id }}" {{ $invoice->contractor_id == $contractor->id ? 'selected' : '' }}>
                                        {{ $contractor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                        <div class="custom-select-wrapper">
                            <select name="payment_method" id="payment_method" required
                                    class="custom-select">
                                <option value="przelew" {{ $invoice->payment_method == 'przelew' ? 'selected' : '' }}>Przelew bankowy</option>
                                <option value="gotowka" {{ $invoice->payment_method == 'gotowka' ? 'selected' : '' }}>Gotówka</option>
                                <option value="karta" {{ $invoice->payment_method == 'karta' ? 'selected' : '' }}>Karta płatnicza</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Daty</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">Data wystawienia</label>
                        <input type="date" name="issue_date" id="issue_date" required value="{{ $invoice->issue_date->format('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-gray-700">Data sprzedaży</label>
                        <input type="date" name="sale_date" id="sale_date" required value="{{ $invoice->sale_date->format('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Termin płatności</label>
                        <input type="date" name="due_date" id="due_date" required value="{{ $invoice->due_date->format('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pozycje faktury -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Pozycje faktury</h2>
                <button type="button" id="add-item" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj pozycję
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ilość</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">J.m.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena netto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">VAT %</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wartość netto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wartość VAT</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wartość brutto</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container" class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                            <tr class="invoice-item">
                                <td class="px-4 py-2">
                                    <input type="text" name="items[][name]" required value="{{ $item->name }}"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[][quantity]" required min="0" step="0.01" value="{{ $item->quantity }}"
                                           class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm quantity">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="items[][unit]" class="table-select w-20">
                                        <option value="szt." {{ $item->unit == 'szt.' ? 'selected' : '' }}>szt.</option>
                                        <option value="kg" {{ $item->unit == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="m" {{ $item->unit == 'm' ? 'selected' : '' }}>m</option>
                                        <option value="m2" {{ $item->unit == 'm2' ? 'selected' : '' }}>m²</option>
                                        <option value="m3" {{ $item->unit == 'm3' ? 'selected' : '' }}>m³</option>
                                        <option value="h" {{ $item->unit == 'h' ? 'selected' : '' }}>h</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[][unit_price]" required min="0" step="0.01" value="{{ $item->unit_price }}"
                                           class="block w-32 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm unit-price">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="items[][tax_rate]" class="table-select w-24 tax-rate">
                                        <option value="23" {{ $item->tax_rate == 23 ? 'selected' : '' }}>23%</option>
                                        <option value="8" {{ $item->tax_rate == 8 ? 'selected' : '' }}>8%</option>
                                        <option value="5" {{ $item->tax_rate == 5 ? 'selected' : '' }}>5%</option>
                                        <option value="0" {{ $item->tax_rate == 0 ? 'selected' : '' }}>0%</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="net-value">{{ number_format($item->net_price, 2) }}</span> PLN
                                </td>
                                <td class="px-4 py-2">
                                    <span class="tax-value">{{ number_format($item->tax_amount, 2) }}</span> PLN
                                </td>
                                <td class="px-4 py-2">
                                    <span class="gross-value">{{ number_format($item->gross_price, 2) }}</span> PLN
                                </td>
                                <td class="px-4 py-2">
                                    <button type="button" class="text-red-600 hover:text-red-900 delete-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-900">Razem:</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-net">{{ number_format($invoice->net_total, 2) }}</span> PLN
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-tax">{{ number_format($invoice->tax_total, 2) }}</span> PLN
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-gross">{{ number_format($invoice->gross_total, 2) }}</span> PLN
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Uwagi -->
        <div class="mt-8">
            <label for="notes" class="block text-sm font-medium text-gray-700">Uwagi</label>
            <textarea name="notes" id="notes" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $invoice->notes }}</textarea>
        </div>

        <!-- Konto bankowe -->
        <div class="mt-8">
            <label for="bank_account_id" class="block text-sm font-medium text-gray-700">Konto bankowe</label>
            <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">{{ __('Wybierz konto bankowe') }}</option>
                @if(auth()->user()->companyProfile)
                    @foreach(auth()->user()->companyProfile->bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}" {{ (old('bank_account_id') ?? $invoice->bank_account_id) == $bankAccount->id ? 'selected' : '' }}>
                            {{ $bankAccount->account_name }} - {{ $bankAccount->account_number }}
                        </option>
                    @endforeach
                @endif
            </select>
            <x-input-error :messages="$errors->get('bank_account_id')" class="mt-2" />
        </div>

        <!-- Przyciski -->
        <div class="mt-8 flex justify-end space-x-3">
            <button type="submit" name="status" value="draft" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Zapisz jako wersję roboczą
            </button>
            <button type="submit" name="status" value="issued"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Wystaw fakturę
            </button>
        </div>
    </form>

    <!-- Podgląd faktury -->
    <div class="mt-10 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Podgląd faktury</h2>
            <div class="text-sm text-gray-500">Podgląd aktualizuje się w czasie rzeczywistym</div>
        </div>

        @if(!auth()->user()->companyProfile)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
            <p class="font-bold">Uwaga!</p>
            <p>Nie skonfigurowano profilu firmy. Niektóre dane mogą być niedostępne w podglądzie. <a href="{{ route('profile.edit') }}" class="underline">Przejdź do ustawień profilu</a> i skonfiguruj profil firmy.</p>
        </div>
        @endif

        <div id="invoice-preview" class="border rounded-lg p-6">
            <!-- Nagłówek faktury -->
            <div class="flex justify-between mb-8">
                <div>
                    <div class="text-2xl font-bold mb-2">Faktura VAT <span id="preview-number">{{ $invoice->number }}</span></div>
                    <div class="text-sm">
                        <div>Data wystawienia: <span id="preview-issue-date">{{ $invoice->issue_date->format('Y-m-d') }}</span></div>
                        <div>Data sprzedaży: <span id="preview-sale-date">{{ $invoice->sale_date->format('Y-m-d') }}</span></div>
                        <div>Termin płatności: <span id="preview-due-date">{{ $invoice->due_date->format('Y-m-d') }}</span></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-semibold mb-2 company-name">
                        @php
                            // Statyczne wartości na wypadek braku danych z profilu firmy
                            $defaultCompanyName = 'KPPRO';
                            $defaultStreet = '';
                            $defaultPostal = '';
                            $defaultCity = '';
                            $defaultNip = '';
                            $defaultInvoiceFooter = '';
                            
                            // Próba pobrania danych z profilu firmy
                            $user = auth()->user();
                            $companyProfile = $user ? $user->companyProfile : null;
                            $companyName = $companyProfile ? $companyProfile->company_name : $defaultCompanyName;
                            $companyStreet = $companyProfile ? $companyProfile->street : $defaultStreet;
                            $companyPostalCode = $companyProfile ? $companyProfile->postal_code : $defaultPostal;
                            $companyCity = $companyProfile ? $companyProfile->city : $defaultCity;
                            $companyTaxNumber = $companyProfile ? $companyProfile->tax_number : $defaultNip;
                            $companyInvoiceFooter = $companyProfile ? $companyProfile->invoice_footer : $defaultInvoiceFooter;
                        @endphp
                        {{ $companyName }}
                    </div>
                    <div class="text-sm">
                        <div class="company-address">{{ $companyStreet }}</div>
                        <div class="company-postal-city">{{ $companyPostalCode }} {{ $companyCity }}</div>
                        <div>NIP: <span class="company-nip">{{ $companyTaxNumber }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Dane sprzedawcy i nabywcy -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <div class="text-sm font-semibold mb-2 text-gray-700">Sprzedawca:</div>
                    <div class="p-3 border rounded bg-gray-50">
                        <div class="font-semibold company-name">{{ $companyName }}</div>
                        <div class="company-address">{{ $companyStreet }}</div>
                        <div class="company-postal-city">{{ $companyPostalCode }} {{ $companyCity }}</div>
                        <div>NIP: <span class="company-nip">{{ $companyTaxNumber }}</span></div>
                        @if(!empty($invoice->bank_account_id) && auth()->user()->companyProfile && 
                            $bankAccount = auth()->user()->companyProfile->bankAccounts->firstWhere('id', $invoice->bank_account_id))
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="text-sm font-semibold">{{ $bankAccount->account_name }}</div>
                                <div class="text-sm">{{ $bankAccount->bank_name }}</div>
                                <div class="text-sm font-medium">{{ $bankAccount->account_number }}</div>
                                @if($bankAccount->swift)
                                    <div class="text-sm">SWIFT: {{ $bankAccount->swift }}</div>
                                @endif
                            </div>
                        @else
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="text-sm">{{ $companyProfile ? $companyProfile->bank_name : '' }}</div>
                                <div class="text-sm font-medium">{{ $companyProfile ? $companyProfile->bank_account : '' }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold mb-2 text-gray-700">Nabywca:</div>
                    <div class="p-3 border rounded bg-gray-50">
                        <div id="preview-contractor-name" class="font-semibold">{{ $invoice->contractor->company_name }}</div>
                        <div id="preview-contractor-address">{{ $invoice->contractor->street }}, {{ $invoice->contractor->postal_code }} {{ $invoice->contractor->city }}</div>
                        <div>NIP: <span id="preview-contractor-nip">{{ $invoice->contractor->nip }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Sposób płatności -->
            <div class="mb-8">
                <div class="text-sm font-semibold mb-2 text-gray-700">Sposób płatności:</div>
                <div id="preview-payment-method" class="p-3 border rounded bg-gray-50">
                    @if($invoice->payment_method == 'przelew')
                        Przelew bankowy
                    @elseif($invoice->payment_method == 'gotowka')
                        Gotówka
                    @elseif($invoice->payment_method == 'karta')
                        Karta płatnicza
                    @else
                        {{ $invoice->payment_method }}
                    @endif
                </div>
            </div>

            <!-- Pozycje faktury -->
            <div class="mb-8">
                <div class="text-sm font-semibold mb-2 text-gray-700">Pozycje faktury:</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 border text-left">Lp.</th>
                                <th class="px-4 py-2 border text-left">Nazwa</th>
                                <th class="px-4 py-2 border text-right">Ilość</th>
                                <th class="px-4 py-2 border text-left">J.m.</th>
                                <th class="px-4 py-2 border text-right">Cena netto</th>
                                <th class="px-4 py-2 border text-right">Wartość netto</th>
                                <th class="px-4 py-2 border text-right">VAT %</th>
                                <th class="px-4 py-2 border text-right">Wartość VAT</th>
                                <th class="px-4 py-2 border text-right">Wartość brutto</th>
                            </tr>
                        </thead>
                        <tbody id="preview-items">
                            <!-- Tu będą wyświetlane pozycje faktury -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="5" class="px-4 py-2 border text-right">Razem:</td>
                                <td class="px-4 py-2 border text-right"><span id="preview-total-net">{{ number_format($invoice->net_total, 2) }}</span> PLN</td>
                                <td class="px-4 py-2 border"></td>
                                <td class="px-4 py-2 border text-right"><span id="preview-total-tax">{{ number_format($invoice->tax_total, 2) }}</span> PLN</td>
                                <td class="px-4 py-2 border text-right"><span id="preview-total-gross">{{ number_format($invoice->gross_total, 2) }}</span> PLN</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Uwagi -->
            <div>
                <div class="text-sm font-semibold mb-2 text-gray-700">Uwagi:</div>
                <div id="preview-notes" class="p-3 border rounded bg-gray-50 min-h-[60px]">{{ $invoice->notes ?: '-' }}</div>
            </div>

            <!-- Stopka faktury -->
            <div class="mt-4">
                <div id="preview-footer" class="p-3 text-sm text-gray-600 text-center">
                    {{ $companyInvoiceFooter }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Szablon pozycji faktury
    const itemTemplate = `
        <tr class="invoice-item">
            <td class="px-4 py-2">
                <input type="text" name="items[][name]" required
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[][quantity]" required min="0" step="0.01" value="1"
                       class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm quantity">
            </td>
            <td class="px-4 py-2">
                <select name="items[][unit]" class="table-select w-20">
                    <option value="szt.">szt.</option>
                    <option value="kg">kg</option>
                    <option value="m">m</option>
                    <option value="m2">m²</option>
                    <option value="m3">m³</option>
                    <option value="h">h</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[][unit_price]" required min="0" step="0.01" value="0.00"
                       class="block w-32 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm unit-price">
            </td>
            <td class="px-4 py-2">
                <select name="items[][tax_rate]" class="table-select w-24 tax-rate">
                    <option value="23">23%</option>
                    <option value="8">8%</option>
                    <option value="5">5%</option>
                    <option value="0">0%</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <span class="net-value">0,00</span> PLN
            </td>
            <td class="px-4 py-2">
                <span class="tax-value">0,00</span> PLN
            </td>
            <td class="px-4 py-2">
                <span class="gross-value">0,00</span> PLN
            </td>
            <td class="px-4 py-2">
                <button type="button" class="text-red-600 hover:text-red-900 delete-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    // Dodawanie nowej pozycji
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        container.insertAdjacentHTML('beforeend', itemTemplate);
        attachEventListeners(container.lastElementChild);
        updatePreview();
    });

    // Usuwanie pozycji
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-item')) {
            e.target.closest('.invoice-item').remove();
            calculateTotals();
            updatePreview();
        }
    });

    // Obliczanie wartości dla pozycji
    function attachEventListeners(row) {
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                calculateRow(row);
                calculateTotals();
                updatePreview();
            });
            input.addEventListener('input', updatePreview);
        });
    }

    function calculateRow(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

        const netValue = quantity * unitPrice;
        const taxValue = netValue * (taxRate / 100);
        const grossValue = netValue + taxValue;

        row.querySelector('.net-value').textContent = netValue.toFixed(2);
        row.querySelector('.tax-value').textContent = taxValue.toFixed(2);
        row.querySelector('.gross-value').textContent = grossValue.toFixed(2);
    }

    function calculateTotals() {
        let totalNet = 0;
        let totalTax = 0;
        let totalGross = 0;

        document.querySelectorAll('.invoice-item').forEach(row => {
            totalNet += parseFloat(row.querySelector('.net-value').textContent);
            totalTax += parseFloat(row.querySelector('.tax-value').textContent);
            totalGross += parseFloat(row.querySelector('.gross-value').textContent);
        });

        document.getElementById('total-net').textContent = totalNet.toFixed(2);
        document.getElementById('total-tax').textContent = totalTax.toFixed(2);
        document.getElementById('total-gross').textContent = totalGross.toFixed(2);
        
        // Aktualizuj podgląd po przeliczeniu sum
        updatePreview();
    }

    // Funkcja aktualizująca podgląd faktury
    function updatePreview() {
        console.log('Aktualizuję podgląd faktury');
        
        // Aktualizacja podstawowych danych
        document.getElementById('preview-number').textContent = document.getElementById('number').value;
        document.getElementById('preview-issue-date').textContent = document.getElementById('issue_date').value;
        document.getElementById('preview-sale-date').textContent = document.getElementById('sale_date').value;
        document.getElementById('preview-due-date').textContent = document.getElementById('due_date').value;
        
        // Dane sprzedawcy już zainicjalizowane bezpośrednio w HTML przy renderowaniu strony
        // Nie musimy tutaj pobierać danych sprzedawcy z AJAX
        
        // Aktualizacja danych kontrahenta
        const contractorSelect = document.getElementById('contractor_id');
        if (contractorSelect.value) {
            const selectedOption = contractorSelect.options[contractorSelect.selectedIndex];
            document.getElementById('preview-contractor-name').textContent = selectedOption.text;
            // Dane kontrahenta pobierzemy z bazy przez AJAX
            fetch(`/contractors/${contractorSelect.value}/json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('preview-contractor-address').textContent = 
                            `${data.contractor.street}, ${data.contractor.postal_code} ${data.contractor.city}`;
                        document.getElementById('preview-contractor-nip').textContent = data.contractor.nip;
                    }
                });
        }
        
        // Aktualizacja metody płatności
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentMethodText = paymentMethodSelect.options[paymentMethodSelect.selectedIndex].text;
        document.getElementById('preview-payment-method').textContent = paymentMethodText;
        
        // Aktualizacja pozycji faktury
        const previewItems = document.getElementById('preview-items');
        previewItems.innerHTML = '';
        
        document.querySelectorAll('.invoice-item').forEach((row, index) => {
            const name = row.querySelector('input[name="items[][name]"]').value.trim() || '-';
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unit = row.querySelector('select[name="items[][unit]"]').value;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;
            const netValue = parseFloat(row.querySelector('.net-value').textContent) || 0;
            const taxValue = parseFloat(row.querySelector('.tax-value').textContent) || 0;
            const grossValue = parseFloat(row.querySelector('.gross-value').textContent) || 0;
            
            const itemRow = document.createElement('tr');
            itemRow.innerHTML = `
                <td class="px-4 py-2 border">${index + 1}</td>
                <td class="px-4 py-2 border">${name}</td>
                <td class="px-4 py-2 border text-right">${quantity.toFixed(2)}</td>
                <td class="px-4 py-2 border">${unit}</td>
                <td class="px-4 py-2 border text-right">${unitPrice.toFixed(2)}</td>
                <td class="px-4 py-2 border text-right">${netValue.toFixed(2)}</td>
                <td class="px-4 py-2 border text-right">${taxRate}%</td>
                <td class="px-4 py-2 border text-right">${taxValue.toFixed(2)}</td>
                <td class="px-4 py-2 border text-right">${grossValue.toFixed(2)}</td>
            `;
            previewItems.appendChild(itemRow);
        });
        
        // Aktualizacja sum
        document.getElementById('preview-total-net').textContent = document.getElementById('total-net').textContent;
        document.getElementById('preview-total-tax').textContent = document.getElementById('total-tax').textContent;
        document.getElementById('preview-total-gross').textContent = document.getElementById('total-gross').textContent;
        
        // Aktualizacja uwag
        const notes = document.getElementById('notes').value.trim();
        document.getElementById('preview-notes').textContent = notes || '-';
    }
    
    // Dodaj nasłuchiwanie zdarzeń dla istniejących pozycji
    document.querySelectorAll('.invoice-item').forEach(row => {
        attachEventListeners(row);
    });
    
    // Nasłuchiwanie zmian w formularzu, aby aktualizować podgląd
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });
    
    // Inicjalizacja podglądu
    updatePreview();
});
</script>
@endpush
@endsection 