@php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Cache;
@endphp

<x-admin-layout>
    <x-slot name="header">
        Panel Administratora
    </x-slot>

    <div class="space-y-6">
        <!-- Panel informacyjny -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Użytkownicy</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $stats['users_count'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Nowych w tym miesiącu: {{ $stats['new_users_this_month'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Online</p>
                            <p class="text-xl font-semibold text-gray-900" id="online-users-count">0</p>
                            <p class="text-xs text-gray-500">Aktywnych użytkowników</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Aktywne subskrypcje</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $stats['active_subscriptions'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">{{ number_format($stats['active_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Statystyki przychodów -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-medium text-gray-900">Statystyki przychodów</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                        <div class="p-4">
                            <p class="text-sm font-medium text-gray-500">Dziś</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['today_subscriptions'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">{{ number_format($stats['today_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                        <div class="p-4">
                            <p class="text-sm font-medium text-gray-500">W tym miesiącu</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['month_subscriptions'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">{{ number_format($stats['month_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                        <div class="p-4">
                            <p class="text-sm font-medium text-gray-500">W tym roku</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['year_subscriptions'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">{{ number_format($stats['year_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                        <div class="p-4">
                            <p class="text-sm font-medium text-gray-500">Razem aktywne</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['total_active_subscriptions'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">{{ number_format($stats['total_active_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <div class="h-60 w-full bg-gradient-to-b from-steel-blue-50 to-white rounded border border-gray-200 flex items-center justify-center">
                            <p class="text-gray-400 text-sm">Wykres przychodów</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel powiadomień subskrypcji -->
            <div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-base font-medium text-gray-900">Powiadomienia subskrypcji</h2>
                        <a href="{{ route('admin.subscriptions.notifications') }}" class="text-xs text-steel-blue-600 hover:text-steel-blue-800">
                            Zobacz wszystkie
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                        @if(isset($subscriptionNotifications) && count($subscriptionNotifications) > 0)
                            @foreach($subscriptionNotifications as $notification)
                                <div class="p-3 hover:bg-gray-50">
                                    <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="p-4 text-center">
                                <p class="text-sm text-gray-500">Brak nowych powiadomień</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Moduły administracyjne -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Moduły administracyjne</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                <a href="{{ route('admin.users.index') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Użytkownicy</p>
                        <p class="text-xs text-gray-500">Zarządzanie użytkownikami</p>
                    </div>
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Subskrypcje</p>
                        <p class="text-xs text-gray-500">Zarządzanie subskrypcjami</p>
                    </div>
                </a>

                <a href="{{ route('admin.subscriptions.alpine') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2l.001 6L10 12l-3.999 4.001L6 22H18v-6l-4-4 4-3.999V2H6z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Subskrypcje Alpine</p>
                        <p class="text-xs text-gray-500">Zarządzanie z Alpine.js</p>
                    </div>
                </a>

                <a href="{{ route('admin.revenue.dashboard') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Przychody</p>
                        <p class="text-xs text-gray-500">Statystyki przychodów</p>
                    </div>
                </a>

                <a href="{{ route('admin.system.logs') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Logi systemowe</p>
                        <p class="text-xs text-gray-500">Przeglądanie logów</p>
                    </div>
                </a>

                <a href="{{ route('admin.system.login-history') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Historia logowań</p>
                        <p class="text-xs text-gray-500">Przeglądanie historii logowań</p>
                    </div>
                </a>

                <a href="{{ route('admin.system.info') }}" class="p-3 rounded-lg border border-gray-200 hover:bg-steel-blue-50 hover:border-steel-blue-200 transition-colors flex items-start">
                    <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Informacje o systemie</p>
                        <p class="text-xs text-gray-500">Dane techniczne</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal ze szczegółami -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-lg w-full max-h-[80vh] overflow-y-auto shadow-xl">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-medium text-gray-900" id="modalTitle">Szczegóły</h3>
                    <button onclick="closeDetails()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4" id="modalContent">
                    <!-- Zawartość modalu będzie wstawiana dynamicznie -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function updateOnlineUsersCount() {
        fetch('/admin/users/online')
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    document.getElementById('online-users-count').textContent = data.data.length;
                }
            })
            .catch(error => console.error('Błąd podczas aktualizacji licznika:', error));
    }

    function showDetails(type) {
        const modal = document.getElementById('detailsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        modalTitle.textContent = type === 'online' ? 'Użytkownicy online' : 'Szczegóły';
        
        fetch(`/admin/users/online`)
            .then(response => response.json())
            .then(data => {
                if (type === 'online' && data.success && Array.isArray(data.data)) {
                    const content = data.data.map(user => `
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">${user.name}</p>
                                <p class="text-xs text-gray-500">${user.email}</p>
                            </div>
                            <div class="text-xs text-gray-500">
                                ${user.last_seen}
                            </div>
                        </div>
                    `).join('');
                    
                    modalContent.innerHTML = content || '<p class="text-gray-500 text-center py-4 text-sm">Brak użytkowników online</p>';
                } else {
                    modalContent.innerHTML = '<p class="text-red-500 text-center py-4 text-sm">Wystąpił błąd podczas ładowania danych</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalContent.innerHTML = '<p class="text-red-500 text-center py-4 text-sm">Wystąpił błąd podczas ładowania danych</p>';
            });
    }

    function closeDetails() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    // Aktualizuj licznik użytkowników online po załadowaniu strony i co 60 sekund
    document.addEventListener('DOMContentLoaded', () => {
        updateOnlineUsersCount();
        setInterval(updateOnlineUsersCount, 60000);
    });
    </script>
    @endpush
</x-admin-layout> 