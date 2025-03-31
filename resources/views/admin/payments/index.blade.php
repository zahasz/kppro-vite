<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Zarządzanie bramkami płatności
            </h2>
            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                <i class="fas fa-plus mr-2"></i> Dodaj nową bramkę
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Logo
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nazwa
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kod
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tryb
                                    </th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kolejność
                                    </th>
                                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Akcje
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($gateways as $gateway)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                            @if ($gateway->logo_path)
                                                <img src="{{ asset('storage/' . $gateway->logo_path) }}" alt="{{ $gateway->name }}" class="h-10">
                                            @else
                                                <span class="text-gray-400"><i class="fas fa-credit-card text-2xl"></i></span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                            {{ $gateway->name }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ $gateway->code }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            @if ($gateway->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktywna
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Nieaktywna
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            @if ($gateway->test_mode)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Testowy
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Produkcyjny
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            {{ $gateway->display_order }}
                                        </td>
                                        <td class="py-4 px-6 text-sm font-medium text-right">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('admin.payments.edit', $gateway->id) }}" class="text-blue-600 hover:text-blue-900" title="Edytuj">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form method="POST" action="{{ route('admin.payments.toggle-status', $gateway->id) }}" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="{{ $gateway->is_active ? 'text-green-600 hover:text-green-900' : 'text-red-600 hover:text-red-900' }}" title="{{ $gateway->is_active ? 'Dezaktywuj' : 'Aktywuj' }}">
                                                        <i class="fas {{ $gateway->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('admin.payments.toggle-test-mode', $gateway->id) }}" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="{{ $gateway->test_mode ? 'text-yellow-600 hover:text-yellow-900' : 'text-blue-600 hover:text-blue-900' }}" title="{{ $gateway->test_mode ? 'Przełącz na tryb produkcyjny' : 'Przełącz na tryb testowy' }}">
                                                        <i class="fas {{ $gateway->test_mode ? 'fa-flask' : 'fa-check-circle' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('admin.payments.destroy', $gateway->id) }}" class="inline-block" onsubmit="return confirm('Czy na pewno chcesz usunąć tę bramkę płatności?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Usuń">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-6 text-sm text-gray-500 text-center">
                                            Brak zdefiniowanych bramek płatności. Kliknij "Dodaj nową bramkę", aby utworzyć pierwszą.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Zarządzanie transakcjami
                        </h3>
                        <a href="{{ route('admin.payments.transactions') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                            <i class="fas fa-list mr-2"></i> Pokaż wszystkie transakcje
                        </a>
                    </div>
                    <p class="text-sm text-gray-600">
                        W sekcji transakcji możesz przeglądać i zarządzać wszystkimi płatnościami dokonanymi przez klientów:
                    </p>
                    <ul class="mt-2 list-disc list-inside text-sm text-gray-600">
                        <li>Monitoruj statusy płatności</li>
                        <li>Przetwarzaj płatności ręcznie (np. dla przelewów bankowych)</li>
                        <li>Obsługuj zwroty płatności</li>
                        <li>Generuj raporty transakcji</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 