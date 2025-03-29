<x-app-layout>
    <x-slot name="header">
        Twoja subskrypcja
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Informacje o subskrypcji</h2>

            @if($subscription)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Informacje o planie -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            <i class="fas fa-tag mr-2"></i>Plan subskrypcji
                        </h3>
                        <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-4">
                            {{ $subscription->subscriptionPlan->name }}
                        </div>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $subscription->subscriptionPlan->description }}
                        </div>
                    </div>

                    <!-- Status i daty -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Status i terminy
                        </h3>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $subscription->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ $subscription->is_active ? 'Aktywna' : 'Nieaktywna' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Data rozpoczęcia:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subscription->start_date->format('d.m.Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Data wygaśnięcia:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subscription->end_date->format('d.m.Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Odnawia się za:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ now()->diffInDays($subscription->end_date) }} dni</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cena i rozliczenia -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            <i class="fas fa-money-bill-wave mr-2"></i>Rozliczenia
                        </h3>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Cena:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ number_format($subscription->subscriptionPlan->price, 2, ',', ' ') }} zł</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">/ {{ $subscription->subscriptionPlan->billing_cycle == 'monthly' ? 'miesiąc' : 'rok' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Metoda płatności:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subscription->payment_method ?? 'Brak informacji' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Następne obciążenie:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subscription->end_date->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dostępne funkcje -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-list-check mr-2"></i>Dostępne funkcje
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $features = json_decode($subscription->subscriptionPlan->features, true) ?? [];
                        @endphp

                        @foreach($features as $feature => $enabled)
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                @if($enabled)
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                @endif
                                <span class="text-gray-700 dark:text-gray-300">
                                    @switch($feature)
                                        @case('finance')
                                            Finanse i księgowość
                                            @break
                                        @case('warehouse')
                                            Magazyn
                                            @break
                                        @case('contractors')
                                            Zarządzanie kontrahentami
                                            @break
                                        @case('invoices')
                                            Faktury
                                            @break
                                        @case('estimates')
                                            Kosztorysy
                                            @break
                                        @case('tasks')
                                            Zadania
                                            @break
                                        @case('contracts')
                                            Umowy
                                            @break
                                        @default
                                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                    @endswitch
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Przycisk do zmiany planu -->
                <div class="mt-8 flex justify-center">
                    <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-arrow-up-right-dots mr-2"></i>Zmień plan subskrypcji
                    </a>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 text-yellow-700 dark:text-yellow-300 p-4 mb-6">
                    <p>Nie masz aktywnej subskrypcji. Wybierz plan, aby korzystać z wszystkich funkcji systemu.</p>
                </div>

                <div class="mt-6 text-center">
                    <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>Wybierz plan subskrypcji
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 