@extends('layouts.app')

@section('title', 'Nowa faktura')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
<style>
    .select2-container {
        z-index: 9999 !important;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
        border-color: rgb(209, 213, 219);
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 400px;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 8px;
        border-radius: 0.375rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Nowa faktura</h1>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
        @csrf
        
        <!-- Dane podstawowe -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Dane podstawowe</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700">Numer faktury</label>
                        <input type="text" name="number" id="number" value="{{ $nextNumber }}" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="contractor_id" class="block text-sm font-medium text-gray-700">Kontrahent</label>
                        <div class="mt-1 flex">
                            <select name="contractor_id" id="contractor_id" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Wybierz kontrahenta</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor->id }}" 
                                            data-nip="{{ $contractor->nip }}"
                                            data-address="{{ $contractor->street }}, {{ $contractor->postal_code }} {{ $contractor->city }}"
                                            data-country="{{ $contractor->country }}"
                                            data-email="{{ $contractor->email }}"
                                            data-phone="{{ $contractor->phone }}">
                                        {{ $contractor->company_name }} (NIP: {{ $contractor->nip }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" 
                                    onclick="document.getElementById('new-contractor-modal').classList.remove('hidden')"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
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
                            <option value="przelew">Przelew bankowy</option>
                            <option value="gotowka">Gotówka</option>
                            <option value="karta">Karta płatnicza</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Daty</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">Data wystawienia</label>
                        <input type="date" name="issue_date" id="issue_date" required value="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-gray-700">Data sprzedaży</label>
                        <input type="date" name="sale_date" id="sale_date" required value="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Termin płatności</label>
                        <input type="date" name="due_date" id="due_date" required value="{{ date('Y-m-d', strtotime('+14 days')) }}"
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
                        <!-- Tutaj będą dodawane dynamicznie pozycje faktury -->
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-900">Razem:</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-net">0,00</span> PLN
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-tax">0,00</span> PLN
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span id="total-gross">0,00</span> PLN
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
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
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

<!-- Modal dodawania nowego kontrahenta -->
<div id="new-contractor-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 my-6">
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Dodaj nowego kontrahenta</h3>
                <button type="button" onclick="closeContractorModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="px-6 py-4" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <form id="new-contractor-form">
                @include('contractors._form')
            </form>
        </div>

        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200 rounded-b-lg">
            <button type="button" onclick="closeContractorModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Anuluj
            </button>
            <button type="button" onclick="document.getElementById('new-contractor-form').submit()"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Dodaj
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

    // Funkcja dodawania nowej pozycji
    function addNewItem() {
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

        const container = document.getElementById('items-container');
        container.insertAdjacentHTML('beforeend', itemTemplate);
        
        const newRow = container.lastElementChild;
        initializeRowEvents(newRow);
        calculateRow(newRow);
        calculateTotals();
    }

    // Inicjalizacja zdarzeń dla wiersza
    function initializeRowEvents(row) {
        const inputs = row.querySelectorAll('input[type="number"], select.tax-rate');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateRow(row);
                calculateTotals();
            });
        });

        const deleteButton = row.querySelector('.delete-item');
        if (deleteButton) {
            deleteButton.addEventListener('click', function() {
                row.remove();
                calculateTotals();
            });
        }
    }

    // Obliczanie wartości dla wiersza
    function calculateRow(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

        const netValue = quantity * unitPrice;
        const taxValue = netValue * (taxRate / 100);
        const grossValue = netValue + taxValue;

        row.querySelector('.net-value').textContent = netValue.toFixed(2).replace('.', ',');
        row.querySelector('.tax-value').textContent = taxValue.toFixed(2).replace('.', ',');
        row.querySelector('.gross-value').textContent = grossValue.toFixed(2).replace('.', ',');
    }

    // Obliczanie sum
    function calculateTotals() {
        let totalNet = 0;
        let totalTax = 0;
        let totalGross = 0;

        document.querySelectorAll('.invoice-item').forEach(row => {
            totalNet += parseFloat(row.querySelector('.net-value').textContent.replace(',', '.')) || 0;
            totalTax += parseFloat(row.querySelector('.tax-value').textContent.replace(',', '.')) || 0;
            totalGross += parseFloat(row.querySelector('.gross-value').textContent.replace(',', '.')) || 0;
        });

        document.getElementById('total-net').textContent = totalNet.toFixed(2).replace('.', ',');
        document.getElementById('total-tax').textContent = totalTax.toFixed(2).replace('.', ',');
        document.getElementById('total-gross').textContent = totalGross.toFixed(2).replace('.', ',');
    }

    // Dodawanie pierwszej pozycji przy załadowaniu strony
    addNewItem();

    // Obsługa przycisku dodawania pozycji
    const addItemButton = document.getElementById('add-item');
    if (addItemButton) {
        addItemButton.addEventListener('click', addNewItem);
    }

    // Obsługa formularza dodawania nowego kontrahenta
    const newContractorForm = document.getElementById('new-contractor-form');
    if (newContractorForm) {
        newContractorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch('{{ route('contractors.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Dodaj nowego kontrahenta do listy Select2
                    const newOption = new Option(data.contractor.company_name + ' (NIP: ' + data.contractor.nip + ')', data.contractor.id, true, true);
                    $('#contractor_id').append(newOption).trigger('change');
                    
                    // Zamknij modal i wyczyść formularz
                    closeContractorModal();
                    newContractorForm.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Wystąpił błąd podczas dodawania kontrahenta');
            });
        });
    }

    // Obsługa formularza faktury
    const invoiceForm = document.querySelector('form');
    invoiceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const items = [];
        document.querySelectorAll('.invoice-item').forEach(row => {
            const name = row.querySelector('input[name="items[][name]"]').value.trim();
            const quantity = parseFloat(row.querySelector('.quantity').value);
            const unitPrice = parseFloat(row.querySelector('.unit-price').value);
            
            if (name && quantity > 0 && unitPrice > 0) {
                items.push({
                    name: name,
                    quantity: quantity,
                    unit: row.querySelector('select[name="items[][unit]"]').value,
                    unit_price: unitPrice,
                    tax_rate: parseFloat(row.querySelector('.tax-rate').value),
                    net_value: parseFloat(row.querySelector('.net-value').textContent.replace(',', '.')),
                    tax_value: parseFloat(row.querySelector('.tax-value').textContent.replace(',', '.')),
                    gross_value: parseFloat(row.querySelector('.gross-value').textContent.replace(',', '.'))
                });
            }
        });

        if (items.length === 0) {
            alert('Dodaj przynajmniej jedną pozycję do faktury');
            return;
        }

        const data = {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            number: this.querySelector('[name="number"]').value,
            contractor_id: this.querySelector('[name="contractor_id"]').value,
            payment_method: this.querySelector('[name="payment_method"]').value,
            issue_date: this.querySelector('[name="issue_date"]').value,
            sale_date: this.querySelector('[name="sale_date"]').value,
            due_date: this.querySelector('[name="due_date"]').value,
            notes: this.querySelector('[name="notes"]').value,
            status: document.activeElement.value,
            items: items,
            total_net: parseFloat(document.getElementById('total-net').textContent.replace(',', '.')),
            total_tax: parseFloat(document.getElementById('total-tax').textContent.replace(',', '.')),
            total_gross: parseFloat(document.getElementById('total-gross').textContent.replace(',', '.'))
        };

        if (!data.contractor_id) {
            alert('Wybierz kontrahenta');
            return;
        }

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || '{{ route('invoices.index') }}';
            } else {
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat();
                    alert('Błędy walidacji:\n' + errorMessages.join('\n'));
                } else {
                    alert(data.message || 'Wystąpił błąd podczas zapisywania faktury');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas zapisywania faktury');
        });
    });
});

function closeContractorModal() {
    document.getElementById('new-contractor-modal').classList.add('hidden');
}
</script>
@endpush
@endsection 