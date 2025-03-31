<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Szczegóły transakcji
            </h2>
            <a href="{{ route('admin.payments.transactions') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                <i class="fas fa-arrow-left mr-2"></i> Powrót do listy transakcji
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje o transakcji</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Dane podstawowe</h4>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">ID transakcji</p>
                                    <p class="font-medium">{{ $transaction->transaction_id }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">ID referencyjne</p>
                                    <p class="font-medium">{{ $transaction->reference_id ?? 'Brak' }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Data utworzenia</p>
                                    <p class="font-medium">{{ $transaction->created_at->format('d.m.Y H:i:s') }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Data aktualizacji</p>
                                    <p class="font-medium">{{ $transaction->updated_at->format('d.m.Y H:i:s') }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Status</p>
                                    <p class="font-medium">
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
                                    </p>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Dane płatności</h4>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Kwota</p>
                                    <p class="font-medium">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Bramka płatności</p>
                                    <p class="font-medium">{{ $transaction->paymentGateway->name ?? $transaction->gateway_code }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Metoda płatności</p>
                                    <p class="font-medium">{{ $transaction->payment_method ?? 'Nieokreślona' }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Szczegóły płatności</p>
                                    <p class="font-medium">{{ $transaction->payment_details ?? 'Brak szczegółów' }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Opis</p>
                                    <p class="font-medium">{{ $transaction->description ?? 'Brak opisu' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Informacje o użytkowniku</h4>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Użytkownik</p>
                                    <p class="font-medium">{{ $transaction->user->name }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-medium">{{ $transaction->user->email }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">ID użytkownika</p>
                                    <p class="font-medium">{{ $transaction->user_id }}</p>
                                </div>
                            </div>
                            
                            @if ($transaction->subscription)
                            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Subskrypcja</h4>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Plan</p>
                                    <p class="font-medium">{{ $transaction->subscription->plan->name }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Status subskrypcji</p>
                                    <p class="font-medium">
                                        @if ($transaction->subscription->status === 'active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktywna
                                            </span>
                                        @elseif ($transaction->subscription->status === 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Oczekująca
                                            </span>
                                        @elseif ($transaction->subscription->status === 'canceled')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Anulowana
                                            </span>
                                        @elseif ($transaction->subscription->status === 'expired')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Wygasła
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Data rozpoczęcia</p>
                                    <p class="font-medium">{{ $transaction->subscription->start_date ? $transaction->subscription->start_date->format('d.m.Y') : 'Nie określono' }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Data zakończenia</p>
                                    <p class="font-medium">{{ $transaction->subscription->end_date ? $transaction->subscription->end_date->format('d.m.Y') : 'Nie określono' }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if ($transaction->invoice)
                            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Faktura</h4>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Numer faktury</p>
                                    <p class="font-medium">{{ $transaction->invoice->number }}</p>
                                </div>
                                
                                <div class="mb-2">
                                    <p class="text-xs text-gray-500">Data wystawienia</p>
                                    <p class="font-medium">{{ $transaction->invoice->created_at->format('d.m.Y') }}</p>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('admin.billing.invoices.pdf', $transaction->invoice->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-file-pdf mr-2"></i> Pobierz fakturę (PDF)
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if ($transaction->error_message)
                    <div class="mt-4 p-4 bg-red-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-red-800 mb-2">Komunikat błędu</h4>
                        <p class="text-sm text-red-700">{{ $transaction->error_message }}</p>
                    </div>
                    @endif
                    
                    @if ($transaction->gateway_response)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Odpowiedź bramki płatności</h4>
                        <pre class="text-xs text-gray-700 bg-white p-3 rounded overflow-x-auto">{{ json_encode($transaction->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Formularz aktualizacji statusu -->
            <div id="status-update" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aktualizacja statusu transakcji</h3>
                    
                    <form method="POST" action="{{ route('admin.payments.update-status', $transaction->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Oczekująca</option>
                                <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Zakończona</option>
                                <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Nieudana</option>
                                <option value="refunded" {{ $transaction->status === 'refunded' ? 'selected' : '' }}>Zwrócona</option>
                                <option value="canceled" {{ $transaction->status === 'canceled' ? 'selected' : '' }}>Anulowana</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notatka (opcjonalnie)</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Notatka będzie widoczna tylko dla administratorów i zostanie zapisana w historii transakcji.</p>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <i class="fas fa-save mr-2"></i> Aktualizuj status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Formularz zwrotu płatności -->
            @if ($transaction->status === 'completed')
            <div id="refund" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Zwrot płatności</h3>
                    
                    <form method="POST" action="{{ route('admin.payments.refund', $transaction->id) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Kwota zwrotu</label>
                            <div class="flex">
                                <input type="number" id="amount" name="amount" value="{{ $transaction->amount }}" min="0.01" max="{{ $transaction->amount }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500">{{ $transaction->currency }}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maksymalna kwota zwrotu: {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Powód zwrotu</label>
                            <input type="text" id="reason" name="reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i> Ta operacja nie może zostać cofnięta.</p>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition" onclick="return confirm('Czy na pewno chcesz dokonać zwrotu tej płatności?');">
                                <i class="fas fa-undo mr-2"></i> Wykonaj zwrot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 