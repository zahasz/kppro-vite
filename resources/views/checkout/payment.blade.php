<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Wybór metody płatności
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Podsumowanie zamówienia</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">{{ $subscription->plan->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $subscription->plan->description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold">{{ number_format($subscription->price, 2) }} PLN</p>
                                    <p class="text-sm text-gray-500">
                                        @if($subscription->plan->billing_period === 'monthly')
                                            Miesięcznie
                                        @elseif($subscription->plan->billing_period === 'quarterly')
                                            Kwartalnie
                                        @elseif($subscription->plan->billing_period === 'yearly')
                                            Rocznie
                                        @else
                                            {{ $subscription->plan->billing_period }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-lg font-medium text-gray-900 mb-4">Wybierz metodę płatności</h2>
                    
                    <form method="POST" action="{{ route('checkout.process', $subscription->id) }}" class="mb-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach($gateways as $gateway)
                                <div class="relative">
                                    <input type="radio" id="gateway_{{ $gateway->code }}" name="gateway" value="{{ $gateway->code }}" class="hidden peer" required>
                                    <label for="gateway_{{ $gateway->code }}" class="block cursor-pointer rounded-lg border-2 border-gray-200 p-4 hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $gateway->name }}</div>
                                            @if($gateway->logo_path)
                                                <img src="{{ asset('storage/' . $gateway->logo_path) }}" alt="{{ $gateway->name }}" class="h-6">
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $gateway->description }}</p>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Zapłać {{ number_format($subscription->price, 2) }} PLN
                                <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Bezpieczna płatność</h3>
                        <p class="text-xs text-gray-500">
                            Wszystkie płatności są przetwarzane w bezpieczny sposób. Twoje dane są chronione 256-bitowym szyfrowaniem SSL. Nie przechowujemy danych Twojej karty płatniczej.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 