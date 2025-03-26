@php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Cache;
@endphp

<x-admin-layout>
    <x-slot name="header">
        Panel Administratora
    </x-slot>

    <div class="p-4">
        <!-- Statystyki -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Statystyki systemu</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Użytkownicy</h3>
                            <p class="text-2xl font-bold">{{ $stats['users_count'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Nowych w tym miesiącu: {{ $stats['new_users_this_month'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Online</h3>
                            <p class="text-2xl font-bold" id="online-users-count">
                                @php
                                    $onlineUsers = Cache::remember('online_users_count', now()->addMinute(), function () {
                                        return \App\Models\User::whereNotNull('last_seen_at')
                                            ->get()
                                            ->filter(function($user) {
                                                return $user->isOnline();
                                            })
                                            ->count();
                                    });
                                @endphp
                                {{ $onlineUsers }}
                            </p>
                            <p class="text-sm text-gray-500">Aktywnych użytkowników</p>
                            <p class="text-xs text-gray-400 mt-1">Ostatnie 5 minut</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button onclick="showDetails('online')" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Zobacz szczegóły
                        </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Aktywne subskrypcje</h3>
                            <p class="text-2xl font-bold">{{ $stats['active_subscriptions'] ?? 0 }}</p>
                            <p class="text-sm text-emerald-500">{{ number_format($stats['active_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statystyki subskrypcji -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Statystyki subskrypcji</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Sprzedane dziś</h3>
                            <p class="text-2xl font-bold">{{ $stats['today_subscriptions'] ?? 0 }}</p>
                            <p class="text-sm text-emerald-500">{{ number_format($stats['today_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">W tym miesiącu</h3>
                            <p class="text-2xl font-bold">{{ $stats['month_subscriptions'] ?? 0 }}</p>
                            <p class="text-sm text-emerald-500">{{ number_format($stats['month_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">W tym roku</h3>
                            <p class="text-2xl font-bold">{{ $stats['year_subscriptions'] ?? 0 }}</p>
                            <p class="text-sm text-emerald-500">{{ number_format($stats['year_subscriptions_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Wszystkie aktywne</h3>
                            <p class="text-2xl font-bold">{{ $stats['total_active_subscriptions'] ?? 0 }}</p>
                            <p class="text-sm text-emerald-500">{{ number_format($stats['total_active_value'] ?? 0, 2) }} zł</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skróty do modułów administracyjnych -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Moduły administracyjne</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.users.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Zarządzanie użytkownikami</h3>
                            <p class="text-gray-500">Dodawanie, edycja i usuwanie użytkowników</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.system.logs') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Logi systemowe</h3>
                            <p class="text-gray-500">Przeglądanie logów systemowych</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.system.login-history') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Historia logowań</h3>
                            <p class="text-gray-500">Przeglądanie historii logowań</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal ze szczegółami -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-lg w-full max-h-[80vh] overflow-y-auto">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Szczegóły</h3>
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
    // Automatyczne odświeżanie licznika użytkowników online
    setInterval(function() {
        fetch('/admin/dashboard/details/online')
            .then(response => response.json())
            .then(data => {
                document.getElementById('online-users-count').textContent = data.data.length;
            });
    }, 60000); // Odświeżaj co minutę

    function showDetails(type) {
        const modal = document.getElementById('detailsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        modalTitle.textContent = type === 'online' ? 'Użytkownicy online' : 'Szczegóły';
        
        fetch(`/admin/dashboard/details/${type}`)
            .then(response => response.json())
            .then(data => {
                if (type === 'online') {
                    const content = data.data.map(user => `
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">${user.name}</p>
                                <p class="text-sm text-gray-500">${user.email}</p>
                            </div>
                            <div class="text-sm text-gray-500">
                                ${user.last_seen}
                            </div>
                        </div>
                    `).join('');
                    
                    modalContent.innerHTML = content || '<p class="text-gray-500 text-center py-4">Brak użytkowników online</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalContent.innerHTML = '<p class="text-red-500 text-center py-4">Wystąpił błąd podczas ładowania danych</p>';
            });
    }

    function closeDetails() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    // Automatyczne odświeżanie licznika użytkowników online
    function updateOnlineUsersCount() {
        fetch('/admin/dashboard/details/online')
            .then(response => response.json())
            .then(data => {
                document.getElementById('online-users-count').textContent = data.data.length;
            })
            .catch(error => console.error('Error:', error));
    }

    // Odświeżaj co 60 sekund
    setInterval(updateOnlineUsersCount, 60000);
    </script>
    @endpush
</x-admin-layout> 