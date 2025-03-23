@php
    use Illuminate\Support\Facades\Schema;
@endphp

<x-admin-layout>
    <x-slot name="header">
        Panel Administratora
    </x-slot>

    <div class="p-4">
        <!-- Statystyki -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Statystyki systemu</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Role</h3>
                            <p class="text-2xl font-bold">{{ $stats['roles_count'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Zarządzaj uprawnieniami</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Uprawnienia</h3>
                            <p class="text-2xl font-bold">{{ $stats['permissions_count'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Przeglądaj uprawnienia</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Aktywni</h3>
                            <p class="text-2xl font-bold">{{ $stats['active_users'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Aktywnych użytkowników</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wykresy i dodatkowe informacje -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Aktywność użytkowników -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Aktywność użytkowników</h3>
                <div class="h-64 flex items-end">
                    @foreach($userActivity as $date => $count)
                        <div class="flex flex-col items-center mx-2">
                            <div class="bg-blue-500 rounded-t-md w-8" style="height: {{ ($count / (max($userActivity) ?: 1)) * 150 }}px;"></div>
                            <span class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($date)->format('d.m') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Informacje o logowaniu -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Statystyki logowania</h3>
                
                @if(Schema::hasTable('login_histories'))
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span>Udane logowania</span>
                            </div>
                            <span class="text-xl font-semibold">{{ $stats['total_login_attempts'] - $stats['failed_login_attempts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <span>Nieudane logowania</span>
                            </div>
                            <span class="text-xl font-semibold">{{ $stats['failed_login_attempts'] }}</span>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.system.login-history') }}" class="text-blue-600 hover:underline flex items-center">
                                Zobacz pełną historię logowania
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Statystyki logowania będą dostępne po utworzeniu tabeli historii logowań.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
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

                <a href="{{ route('admin.roles.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Zarządzanie rolami</h3>
                            <p class="text-gray-500">Tworzenie i zarządzanie rolami użytkowników</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.permissions.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Uprawnienia</h3>
                            <p class="text-gray-500">Zarządzanie uprawnieniami systemowymi</p>
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
                            <p class="text-gray-500">Przeglądaj logi systemu</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.system.info') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Informacje o systemie</h3>
                            <p class="text-gray-500">Szczegóły konfiguracji</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.system.backup') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Kopie zapasowe</h3>
                            <p class="text-gray-500">Zarządzaj kopiami zapasowymi systemu</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Plany subskrypcji</h3>
                            <p class="text-gray-500">Zarządzaj planami subskrypcji i limitami</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.subscriptions.users') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Subskrypcje użytkowników</h3>
                            <p class="text-gray-500">Przeglądaj i zarządzaj subskrypcjami</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-admin-layout> 