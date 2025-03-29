@php
    use Illuminate\Support\Facades\Schema;
    $tableExists = Schema::hasTable('login_histories');
    $recordCount = $loginHistory->count();
@endphp

<x-admin-layout>
    <x-slot name="header">
        Historia logowań
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Historia logowań użytkowników</h2>

            <!-- Diagnostyka -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Diagnostyka: Tabela login_histories {{ $tableExists ? 'istnieje' : 'nie istnieje' }}.
                            Liczba rekordów: {{ $recordCount }}.
                        </p>
                    </div>
                </div>
            </div>

            @if($tableExists)
                <!-- Formularz filtrowania -->
                <form action="{{ route('admin.system.login-history') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg" data-login-filter>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Użytkownik</label>
                            <input type="text" name="user" id="user" class="shadow-sm focus:ring-steel-blue-500 focus:border-steel-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nazwisko lub email" value="{{ request('user') }}">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="shadow-sm focus:ring-steel-blue-500 focus:border-steel-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Wszystkie</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Udane</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Nieudane</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                            <input type="date" name="date_from" id="date_from" class="shadow-sm focus:ring-steel-blue-500 focus:border-steel-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ request('date_from') }}">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                            <input type="date" name="date_to" id="date_to" class="shadow-sm focus:ring-steel-blue-500 focus:border-steel-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('admin.system.login-history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 mr-3">
                            Wyczyść
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-steel-blue-600 hover:bg-steel-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                            Filtruj
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" data-login-history>
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Użytkownik
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Adres IP
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Przeglądarka
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($recordCount > 0)
                                @foreach($loginHistory as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $log->user->name ?? 'Nieznany' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $log->user->email ?? 'Brak adresu' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $log->ip_address ?? 'Nieznany' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $log->browser_info }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $log->status === 'success' ? 'Udane' : 'Nieudane' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Brak wpisów w historii logowania
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($recordCount > 0)
                    <div class="mt-4">
                        {{ $loginHistory->links() }}
                    </div>
                @endif
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Tabela historii logowań nie została jeszcze utworzona.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout> 