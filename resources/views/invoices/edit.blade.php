@extends('layouts.app')

@section('title', 'Edycja faktury')

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
                        <select name="contractor_id" id="contractor_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Wybierz kontrahenta</option>
                            @foreach($contractors as $contractor)
                                <option value="{{ $contractor->id }}" 
                                        data-nip="{{ $contractor->nip }}"
                                        data-address="{{ $contractor->street }}, {{ $contractor->postal_code }} {{ $contractor->city }}"
                                        data-country="{{ $contractor->country }}"
                                        data-email="{{ $contractor->email }}"
                                        data-phone="{{ $contractor->phone }}"
                                        {{ $invoice->contractor_id == $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->company_name }} (NIP: {{ $contractor->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dane kontrahenta -->
                    <div id="contractor-details" class="hidden mt-4 p-4 bg-gray-50 rounded-md">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Dane kontrahenta</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Nazwa:</span> <span id="contractor-name"></span></p>
                            <p><span class="font-medium">NIP:</span> <span id="contractor-nip"></span></p>
                            <p><span class="font-medium">Adres:</span> <span id="contractor-address"></span></p>
                            <p><span class="font-medium">Kraj:</span> <span id="contractor-country"></span></p>
                            <p><span class="font-medium">Email:</span> <span id="contractor-email"></span></p>
                            <p><span class="font-medium">Telefon:</span> <span id="contractor-phone"></span></p>
                        </div>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                        <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="przelew" {{ $invoice->payment_method == 'przelew' ? 'selected' : '' }}>Przelew bankowy</option>
                            <option value="gotowka" {{ $invoice->payment_method == 'gotowka' ? 'selected' : '' }}>Gotówka</option>
                            <option value="karta" {{ $invoice->payment_method == 'karta' ? 'selected' : '' }}>Karta płatnicza</option>
                        </select>
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
                                    <select name="items[][unit]" class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                                    <select name="items[][tax_rate]" class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm tax-rate">
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicjalizacja Select2 dla wyboru kontrahenta
    const contractorSelect = $('#contractor_id');
    contractorSelect.select2({
        placeholder: 'Wybierz kontrahenta',
        allowClear: true,
        width: '100%',
        dropdownParent: document.body
    });

    // Obsługa zmiany kontrahenta
    contractorSelect.on('select2:select select2:unselect', function(e) {
        const selectedOption = $(this).find(':selected');
        const contractorDetails = document.getElementById('contractor-details');
        
        if (selectedOption.val()) {
            document.getElementById('contractor-name').textContent = selectedOption.text().split(' (NIP:')[0];
            document.getElementById('contractor-nip').textContent = selectedOption.data('nip');
            document.getElementById('contractor-address').textContent = selectedOption.data('address');
            document.getElementById('contractor-country').textContent = selectedOption.data('country');
            document.getElementById('contractor-email').textContent = selectedOption.data('email');
            document.getElementById('contractor-phone').textContent = selectedOption.data('phone');
            contractorDetails.classList.remove('hidden');
        } else {
            contractorDetails.classList.add('hidden');
        }
    });

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
                <select name="items[][unit]" class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                <select name="items[][tax_rate]" class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm tax-rate">
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
    });

    // Usuwanie pozycji
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-item')) {
            e.target.closest('.invoice-item').remove();
            calculateTotals();
        }
    });

    // Obliczanie wartości dla pozycji
    function attachEventListeners(row) {
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                calculateRow(row);
                calculateTotals();
            });
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
    }

    // Dodaj nasłuchiwanie zdarzeń dla istniejących pozycji
    document.querySelectorAll('.invoice-item').forEach(row => {
        attachEventListeners(row);
    });
});
</script>
@endpush
@endsection 