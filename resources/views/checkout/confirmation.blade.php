<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Potwierdzenie płatności
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        @if($transaction->status === 'completed')
                            <div class="mb-4">
                                <svg class="h-16 w-16 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Płatność zakończona pomyślnie!</h1>
                            <p class="text-gray-600">Dziękujemy za zakup subskrypcji. Twoja płatność została przetworzona i potwierdzamy aktywację Twojego konta.</p>
                        @elseif($transaction->status === 'pending')
                            <div class="mb-4">
                                <svg class="h-16 w-16 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Oczekiwanie na potwierdzenie płatności</h1>
                            <p class="text-gray-600">Twoja płatność jest przetwarzana. Potwierdzenie otrzymasz na adres email, gdy proces zostanie zakończony.</p>
                            
                            @if($transaction->payment_method === 'bank_transfer')
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg max-w-md mx-auto">
                                    <h2 class="text-lg font-semibold text-blue-800 mb-2">Instrukcja płatności przelewem</h2>
                                    <p class="text-sm text-blue-700 mb-4">Prosimy o wykonanie przelewu na poniższe dane:</p>
                                    
                                    <div class="text-left text-sm">
                                        <p class="mb-1"><span class="font-medium">Odbiorca:</span> {{ $transaction->paymentGateway->getConfig('account_name', 'Nazwa Firmy Sp. z o.o.') }}</p>
                                        <p class="mb-1"><span class="font-medium">Numer konta:</span> {{ $transaction->paymentGateway->getConfig('account_number', 'PL00 0000 0000 0000 0000 0000 0000') }}</p>
                                        <p class="mb-1"><span class="font-medium">Bank:</span> {{ $transaction->paymentGateway->getConfig('bank_name', 'Bank Polski') }}</p>
                                        <p class="mb-1"><span class="font-medium">Kwota:</span> {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                                        <p class="mb-1"><span class="font-medium">Tytuł przelewu:</span> Subskrypcja {{ $transaction->transaction_id }}</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="mb-4">
                                <svg class="h-16 w-16 text-red-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Wystąpił problem z płatnością</h1>
                            <p class="text-gray-600">{{ $transaction->error_message ?? 'Przepraszamy, ale wystąpił problem podczas przetwarzania płatności. Prosimy o kontakt z obsługą klienta.' }}</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Szczegóły transakcji</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Numer transakcji</p>
                                <p class="font-medium">{{ $transaction->transaction_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data</p>
                                <p class="font-medium">{{ $transaction->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kwota</p>
                                <p class="font-medium">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="font-medium">
                                    @if($transaction->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Opłacona
                                        </span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Oczekująca
                                        </span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Nieudana
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $transaction->status }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Metoda płatności</p>
                                <p class="font-medium">{{ $transaction->payment_method ?? $transaction->paymentGateway->name }}</p>
                            </div>
                        </div>
                    </div>

                    @if($transaction->subscription)
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Subskrypcja</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Plan</p>
                                <p class="font-medium">{{ $transaction->subscription->plan->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="font-medium">
                                    @if($transaction->subscription->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktywna
                                        </span>
                                    @elseif($transaction->subscription->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Oczekująca
                                        </span>
                                    @elseif($transaction->subscription->status === 'canceled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Anulowana
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $transaction->subscription->status }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data rozpoczęcia</p>
                                <p class="font-medium">{{ $transaction->subscription->start_date->format('d.m.Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data zakończenia</p>
                                <p class="font-medium">{{ $transaction->subscription->end_date->format('d.m.Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($transaction->invoice)
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Faktura</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Numer faktury</p>
                                <p class="font-medium">{{ $transaction->invoice->number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data wystawienia</p>
                                <p class="font-medium">{{ $transaction->invoice->created_at->format('d.m.Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-center">
                            <a href="{{ route('billing.invoice.download', $transaction->invoice->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Pobierz fakturę (PDF)
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="flex justify-center mt-8">
                        <a href="{{ route('user.subscriptions') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Przejdź do panelu subskrypcji
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 