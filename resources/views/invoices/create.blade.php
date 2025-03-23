@extends('layouts.app')

@section('title', 'Utwórz fakturę')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Nagłówek sekcji -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Nowa faktura</h1>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>

    <!-- Formularz faktury -->
    <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        
        <!-- Informacje podstawowe -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informacje podstawowe</h2>
                
                <div class="mb-4">
                    <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Numer faktury</label>
                    <input type="text" id="number" name="number" value="{{ $nextNumber }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                
                <div class="mb-4">
                    <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Data wystawienia</label>
                    <input type="date" id="issue_date" name="issue_date" value="{{ date('Y-m-d') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                
                <div class="mb-4">
                    <label for="sale_date" class="block text-sm font-medium text-gray-700 mb-1">Data sprzedaży</label>
                    <input type="date" id="sale_date" name="sale_date" value="{{ date('Y-m-d') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                
                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Termin płatności</label>
                    <input type="date" id="due_date" name="due_date" value="{{ $defaultDueDate ?? date('Y-m-d', strtotime('+14 days')) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Kontrahent</h2>
                
                <div class="mb-4">
                    <label for="contractor_id" class="block text-sm font-medium text-gray-700 mb-1">Wybierz kontrahenta</label>
                    <select id="contractor_id" name="contractor_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- Wybierz kontrahenta --</option>
                        @foreach($contractors as $contractor)
                        <option value="{{ $contractor->id }}">{{ $contractor->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Dane kontrahenta -->
                <div id="contractor_details" class="bg-gray-50 p-4 rounded-md mb-4 {{ old('contractor_id') ? '' : 'hidden' }}">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Dane kontrahenta:</h3>
                    <p id="contractor_name" class="text-sm font-medium"></p>
                    <p id="contractor_tax_id" class="text-sm"></p>
                    <p id="contractor_address" class="text-sm"></p>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Sprzedawca</h2>
                
                @if($companyProfile)
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Dane Twojej firmy:</h3>
                    <p class="text-sm font-medium">{{ $companyProfile->company_name }}</p>
                    <p class="text-sm">NIP: {{ $companyProfile->tax_number }}</p>
                    <p class="text-sm">{{ $companyProfile->street }}</p>
                    <p class="text-sm">{{ $companyProfile->postal_code }} {{ $companyProfile->city }}</p>
                    <p class="text-sm">{{ $companyProfile->phone }}</p>
                    <p class="text-sm">{{ $companyProfile->email }}</p>
                </div>
                
                @if($bankAccounts && $bankAccounts->count() > 0 && $defaultPaymentMethod === 'przelew')
                <div class="mt-4 bg-gray-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Twoje konto bankowe:</h3>
                    @if($companyProfile->defaultBankAccount)
                        <p class="text-sm">{{ $companyProfile->defaultBankAccount->bank_name }}</p>
                        <p class="text-sm font-medium">{{ $companyProfile->defaultBankAccount->account_number }}</p>
                    @else
                        <p class="text-sm">Wybierz konto bankowe w opcjach faktury.</p>
                    @endif
                </div>
                @endif
                
                @else
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
                    <p class="text-sm text-yellow-700">
                        Nie masz jeszcze uzupełnionych danych firmy. 
                        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">
                            Uzupełnij profil firmy
                        </a>, aby automatycznie wypełniać dane sprzedawcy na fakturach.
                    </p>
                </div>
                @endif
            </div>
            
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Płatność</h2>
                
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metoda płatności</label>
                    <select id="payment_method" name="payment_method" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="przelew" {{ ($defaultPaymentMethod ?? '') === 'przelew' ? 'selected' : '' }}>Przelew</option>
                        <option value="gotówka" {{ ($defaultPaymentMethod ?? '') === 'gotówka' ? 'selected' : '' }}>Gotówka</option>
                        <option value="karta" {{ ($defaultPaymentMethod ?? '') === 'karta' ? 'selected' : '' }}>Karta płatnicza</option>
                        <option value="blik" {{ ($defaultPaymentMethod ?? '') === 'blik' ? 'selected' : '' }}>BLIK</option>
                    </select>
                </div>
                
                <div id="bank_account_container" class="mb-4 {{ ($defaultPaymentMethod ?? 'przelew') !== 'przelew' ? 'hidden' : '' }}">
                    <label for="bank_account_id" class="block text-sm font-medium text-gray-700 mb-1">Konto bankowe</label>
                    @if(isset($bankAccounts) && $bankAccounts->count() > 0)
                        <select id="bank_account_id" name="bank_account_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">-- Wybierz konto bankowe --</option>
                            @foreach($bankAccounts as $bankAccount)
                                <option value="{{ $bankAccount->id }}" {{ $bankAccount->is_default ? 'selected' : '' }}>
                                    {{ $bankAccount->account_name }} ({{ $bankAccount->bank_name }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="mt-1 text-sm text-gray-600">
                            Brak kont bankowych. <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800">Dodaj konto bankowe</a> w profilu firmy.
                        </div>
                    @endif
                </div>
                
                <div class="mb-4">
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Waluta</label>
                    <select id="currency" name="currency" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="PLN" {{ ($defaultCurrency ?? 'PLN') === 'PLN' ? 'selected' : '' }}>PLN</option>
                        <option value="EUR" {{ ($defaultCurrency ?? '') === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="USD" {{ ($defaultCurrency ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="GBP" {{ ($defaultCurrency ?? '') === 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Pozycje faktury -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Pozycje faktury</h2>
                <button type="button" id="addItem" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i>
                    Dodaj pozycję
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">J.m.</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena netto</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT %</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość netto</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość VAT</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość brutto</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody id="items_container" class="bg-white divide-y divide-gray-200">
                        <!-- Tutaj będą dodawane dynamicznie wiersze z pozycjami faktury -->
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-900">Razem:</td>
                            <td class="px-3 py-3 text-sm text-gray-900"><span id="total_net_value">0,00</span> <span class="currency">PLN</span></td>
                            <td class="px-3 py-3 text-sm text-gray-900"><span id="total_tax_value">0,00</span> <span class="currency">PLN</span></td>
                            <td class="px-3 py-3 text-sm text-gray-900"><span id="total_gross_value">0,00</span> <span class="currency">PLN</span></td>
                            <td class="px-3 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Uwagi -->
        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Uwagi</label>
            <textarea id="notes" name="notes" rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
        </div>
        
        <!-- Przyciski akcji -->
        <div class="flex justify-end space-x-4">
            <input type="hidden" name="status" id="invoice_status" value="draft">
            <input type="hidden" name="total_net" id="total_net">
            <input type="hidden" name="total_tax" id="total_tax">
            <input type="hidden" name="total_gross" id="total_gross">
            
            <button type="submit" name="status" value="draft" id="saveDraftBtn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-save mr-2"></i>
                Zapisz jako roboczą
            </button>
            <button type="submit" name="status" value="issued" id="saveInvoiceBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-check mr-2"></i>
                Wystaw fakturę
            </button>
        </div>
    </form>
</div>

<!-- Szablon wiersza pozycji faktury -->
<template id="invoice_item_template">
    <tr class="item-row">
        <td class="px-3 py-2">
            <input type="text" name="items[{INDEX}][name]" class="item-name block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[{INDEX}][quantity]" class="item-quantity block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="1" min="0.01" step="0.01" required>
        </td>
        <td class="px-3 py-2">
            <select name="items[{INDEX}][unit]" class="item-unit block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                <option value="szt">szt</option>
                <option value="godz">godz</option>
                <option value="usł">usł</option>
                <option value="kg">kg</option>
                <option value="m">m</option>
                <option value="m2">m²</option>
                <option value="m3">m³</option>
                <option value="km">km</option>
                <option value="opak">opak</option>
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[{INDEX}][unit_price]" class="item-unit-price block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="0" min="0" step="0.01" required>
        </td>
        <td class="px-3 py-2">
            <select name="items[{INDEX}][tax_rate]" class="item-tax-rate block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                <option value="23">23%</option>
                <option value="8">8%</option>
                <option value="5">5%</option>
                <option value="0">0%</option>
                <option value="zw">zw</option>
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[{INDEX}][net_value]" class="item-net-value block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="0" min="0" step="0.01" readonly>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[{INDEX}][tax_value]" class="item-tax-value block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="0" min="0" step="0.01" readonly>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[{INDEX}][gross_value]" class="item-gross-value block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="0" min="0" step="0.01" readonly>
        </td>
        <td class="px-3 py-2">
            <button type="button" class="remove-item text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicjalizacja Tom Select dla wyboru kontrahenta
        new TomSelect('#contractor_id', {
            placeholder: 'Wybierz kontrahenta',
            allowEmptyOption: true
        });
        
        // Licznik dla indeksowania pozycji faktury
        let itemIndex = 0;
        
        // Dodawanie pozycji faktury
        document.getElementById('addItem').addEventListener('click', function() {
            const template = document.getElementById('invoice_item_template').innerHTML;
            const itemHtml = template.replace(/{INDEX}/g, itemIndex++);
            document.getElementById('items_container').insertAdjacentHTML('beforeend', itemHtml);
            setupItemEventListeners();
        });
        
        // Funkcja ustawiająca nasłuchiwanie zdarzeń dla pozycji faktury
        function setupItemEventListeners() {
            // Usuwanie pozycji
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('tr').remove();
                    recalculateTotals();
                });
            });
            
            // Obliczanie wartości dla pozycji
            document.querySelectorAll('.item-row').forEach(row => {
                const quantityInput = row.querySelector('.item-quantity');
                const unitPriceInput = row.querySelector('.item-unit-price');
                const taxRateSelect = row.querySelector('.item-tax-rate');
                
                [quantityInput, unitPriceInput, taxRateSelect].forEach(el => {
                    el.addEventListener('change', function() {
                        calculateItemValues(row);
                        recalculateTotals();
                    });
                    el.addEventListener('input', function() {
                        calculateItemValues(row);
                        recalculateTotals();
                    });
                });
            });
        }
        
        // Funkcja obliczająca wartości dla pozycji faktury
        function calculateItemValues(row) {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-unit-price').value) || 0;
            const taxRateStr = row.querySelector('.item-tax-rate').value;
            
            let taxRate = 0;
            if (taxRateStr !== 'zw') {
                taxRate = parseFloat(taxRateStr) || 0;
            }
            
            const netValue = quantity * unitPrice;
            const taxValue = taxRateStr !== 'zw' ? netValue * (taxRate / 100) : 0;
            const grossValue = netValue + taxValue;
            
            row.querySelector('.item-net-value').value = netValue.toFixed(2);
            row.querySelector('.item-tax-value').value = taxValue.toFixed(2);
            row.querySelector('.item-gross-value').value = grossValue.toFixed(2);
        }
        
        // Funkcja przeliczająca sumy
        function recalculateTotals() {
            let totalNet = 0;
            let totalTax = 0;
            let totalGross = 0;
            
            document.querySelectorAll('.item-row').forEach(row => {
                totalNet += parseFloat(row.querySelector('.item-net-value').value) || 0;
                totalTax += parseFloat(row.querySelector('.item-tax-value').value) || 0;
                totalGross += parseFloat(row.querySelector('.item-gross-value').value) || 0;
            });
            
            document.getElementById('total_net_value').textContent = totalNet.toFixed(2);
            document.getElementById('total_tax_value').textContent = totalTax.toFixed(2);
            document.getElementById('total_gross_value').textContent = totalGross.toFixed(2);
            
            document.getElementById('total_net').value = totalNet.toFixed(2);
            document.getElementById('total_tax').value = totalTax.toFixed(2);
            document.getElementById('total_gross').value = totalGross.toFixed(2);
        }
        
        // Wyświetlanie danych kontrahenta po wyborze
        document.getElementById('contractor_id').addEventListener('change', function() {
            const contractorId = this.value;
            if (contractorId) {
                fetch(`/contractors/${contractorId}/json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.contractor) {
                            const contractor = data.contractor;
                            document.getElementById('contractor_name').textContent = contractor.company_name;
                            document.getElementById('contractor_tax_id').textContent = contractor.nip;
                            document.getElementById('contractor_address').textContent = `${contractor.street}, ${contractor.postal_code} ${contractor.city}`;
                            document.getElementById('contractor_details').classList.remove('hidden');
                        } else {
                            console.error('Błąd pobierania danych kontrahenta');
                        }
                    })
                    .catch(error => {
                        console.error('Błąd:', error);
                    });
            } else {
                document.getElementById('contractor_details').classList.add('hidden');
            }
        });
        
        // Obsługa metody płatności
        document.getElementById('payment_method').addEventListener('change', function() {
            const bankAccountContainer = document.getElementById('bank_account_container');
            if (this.value === 'przelew') {
                bankAccountContainer.classList.remove('hidden');
            } else {
                bankAccountContainer.classList.add('hidden');
            }
        });
        
        // Zmiana waluty we wszystkich miejscach
        document.getElementById('currency').addEventListener('change', function() {
            const currencyCode = this.value;
            document.querySelectorAll('.currency').forEach(el => {
                el.textContent = currencyCode;
            });
        });
        
        // Walidacja formularza przed wysłaniem
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            // Sprawdzamy czy formularz ma pozycje faktury
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('Dodaj przynajmniej jedną pozycję do faktury!');
                return false;
            }
            
            // Sprawdzamy czy wybrano kontrahenta
            const contractorId = document.getElementById('contractor_id').value;
            if (!contractorId) {
                e.preventDefault();
                alert('Wybierz kontrahenta!');
                return false;
            }
            
            // Sprawdzamy, czy wszystkie wymagane pola są wypełnione
            let valid = true;
            items.forEach((row, index) => {
                const name = row.querySelector('.item-name').value.trim();
                const quantity = parseFloat(row.querySelector('.item-quantity').value);
                const unitPrice = parseFloat(row.querySelector('.item-unit-price').value);
                
                if (!name || quantity <= 0 || unitPrice <= 0) {
                    valid = false;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Uzupełnij poprawnie wszystkie pozycje faktury!');
                return false;
            }
            
            // Aktualizujemy hidden inputs z wartościami przed wysłaniem
            recalculateTotals();
        });
        
        // Dodaj pierwszą pozycję faktury na starcie
        document.getElementById('addItem').click();
    });
</script>
@endsection 