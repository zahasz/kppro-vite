<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historia transakcji płatności
            </h2>
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                <i class="fas fa-credit-card mr-2"></i> Zarządzanie bramkami
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filtrowanie transakcji</h3>
                    
                    <form method="GET" action="{{ route('admin.payments.transactions') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Wszystkie statusy</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="gateway" class="block text-sm font-medium text-gray-700 mb-1">Bramka płatności</label>
                                <select id="gateway" name="gateway" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Wszystkie bramki</option>
                                    @foreach($gateways as $gateway)
                                        <option value="{{ $gateway->code }}" {{ request('gateway') == $gateway->code ? 'selected' : '' }}>{{ $gateway->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('admin.payments.transactions') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition mr-2">
                                Wyczyść filtry
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <i class="fas fa-search mr-2"></i> Filtruj
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID transakcji
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Użytkownik
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kwota
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bramka
                                    </th>
                                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Akcje
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                            {{ Str::limit($transaction->transaction_id, 15) }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ $transaction->user->name }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            @if ($transaction->status === 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Zakończona
                                                </span>
                                            @elseif ($transaction->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Oczekująca
                                                </span>
                                            @elseif ($transaction->status === 'failed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Nieudana
                                                </span>
                                            @elseif ($transaction->status === 'refunded')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Zwrócona
                                                </span>
                                            @elseif ($transaction->status === 'canceled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Anulowana
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ $transaction->paymentGateway->name ?? $transaction->gateway_code }}
                                        </td>
                                        <td class="py-4 px-6 text-sm font-medium text-right">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('admin.payments.transaction-details', $transaction->id) }}" class="text-blue-600 hover:text-blue-900" title="Szczegóły">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if ($transaction->status === 'pending')
                                                    <a href="{{ route('admin.payments.transaction-details', $transaction->id) }}#status-update" class="text-yellow-600 hover:text-yellow-900" title="Aktualizuj status">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if ($transaction->status === 'completed')
                                                    <a href="{{ route('admin.payments.transaction-details', $transaction->id) }}#refund" class="text-purple-600 hover:text-purple-900" title="Zwróć płatność">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-6 text-sm text-gray-500 text-center">
                                            Brak transakcji płatności pasujących do kryteriów wyszukiwania.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie transakcji</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-blue-500 text-3xl font-bold">
                                {{ $transactions->where('status', 'completed')->sum('amount') }} PLN
                            </div>
                            <div class="text-blue-700 text-sm mt-1">
                                Łączna wartość zakończonych transakcji
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="text-yellow-500 text-3xl font-bold">
                                {{ $transactions->where('status', 'pending')->count() }}
                            </div>
                            <div class="text-yellow-700 text-sm mt-1">
                                Oczekujące transakcje
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-green-500 text-3xl font-bold">
                                {{ $transactions->where('status', 'completed')->count() }}
                            </div>
                            <div class="text-green-700 text-sm mt-1">
                                Zakończone transakcje
                            </div>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="text-red-500 text-3xl font-bold">
                                {{ $transactions->whereIn('status', ['failed', 'canceled'])->count() }}
                            </div>
                            <div class="text-red-700 text-sm mt-1">
                                Nieudane/anulowane transakcje
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 