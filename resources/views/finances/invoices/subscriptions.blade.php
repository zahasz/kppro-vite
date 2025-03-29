@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">Faktury subskrypcyjne</h1>
    
    <div class="bg-white shadow overflow-hidden rounded-lg mb-6">
        <div class="flex flex-wrap">
            <a href="{{ route('finance.invoices') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Wszystkie
            </a>
            <a href="{{ route('finance.invoices.sales') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.sales') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Sprzedaż
            </a>
            <a href="{{ route('finance.invoices.purchases') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.purchases') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Zakupy
            </a>
            <a href="{{ route('finance.invoices.subscriptions') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.subscriptions') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Subskrypcje
            </a>
        </div>
    </div>
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium">Lista faktur subskrypcyjnych</h2>
            <p class="text-gray-600 text-sm">Zarządzaj fakturami generowanymi automatycznie dla subskrypcji</p>
        </div>
        <div>
            <button onclick="runGenerateInvoices()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
                Generuj faktury subskrypcyjne
            </button>
        </div>
    </div>
    
    <!-- Filtry -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status faktury</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="issued">Wystawione</option>
                    <option value="paid">Opłacone</option>
                    <option value="overdue">Zaległe</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Data od</label>
                <input type="date" id="date_from" name="date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Data do</label>
                <input type="date" id="date_to" name="date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
                <input type="text" id="search" name="search" placeholder="Numer faktury, nazwa klienta..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
                Filtruj
            </button>
        </div>
    </div>
    
    <!-- Tabela faktur -->
    <div class="bg-white shadow overflow-hidden rounded-lg">
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
                        Plan subskrypcyjny
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data wystawienia
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Termin płatności
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kwota
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
                <!-- Dane przykładowe - docelowo będzie to pętla -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        FV/AUT/04/2023/001
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ACME Sp. z o.o.
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Business Pro
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        01.04.2023
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        15.04.2023
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        199,00 PLN
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Opłacona
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Podgląd</a>
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">PDF</a>
                        <a href="#" class="text-blue-600 hover:text-blue-900">Wyślij</a>
                    </td>
                </tr>
                <!-- Więcej wierszy... -->
            </tbody>
        </table>
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <!-- Paginacja -->
            <nav class="flex items-center justify-between">
                <div class="flex-1 flex justify-between items-center">
                    <p class="text-sm text-gray-700">
                        Wyświetlanie <span class="font-medium">1</span> do <span class="font-medium">10</span> z <span class="font-medium">20</span> wyników
                    </p>
                    <div>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Poprzednia
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Następna
                            </a>
                        </span>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>

<script>
    function runGenerateInvoices() {
        if (confirm('Czy na pewno chcesz uruchomić generowanie faktur subskrypcyjnych? Ta operacja może potrwać kilka minut.')) {
            // Pokazujemy spinner lub informację o ładowaniu
            const button = document.querySelector('button[onclick="runGenerateInvoices()"]');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generowanie...';
            
            // Wywołujemy endpoint do generowania faktur
            fetch('{{ route('finance.invoices.subscriptions.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Rozpoczęto generowanie faktur. Proces zostanie wykonany w tle.');
                    // Odświeżamy stronę po 2 sekundach
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Wystąpił błąd: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                alert('Wystąpił błąd podczas generowania faktur.');
            })
            .finally(() => {
                // Przywracamy przycisk do oryginalnego stanu
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }
    }
</script>
@endsection 