@php
    use Illuminate\Support\Facades\Schema;
@endphp

<x-admin-layout>
    <x-slot name="header">
        Kopie zapasowe systemu
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Zarządzanie kopiami zapasowymi</h2>

            <div class="mb-6">
                <form action="{{ route('admin.system.backup.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                        <i class="fas fa-plus-circle mr-2"></i> Utwórz nową kopię zapasową
                    </button>
                </form>
            </div>

            @if(count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nazwa pliku
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rozmiar
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data utworzenia
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Akcje
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($backups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.backup.download', ['filename' => $backup['name']]) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-download"></i> Pobierz
                                            </a>
                                            <form action="{{ route('admin.backup.delete', ['filename' => $backup['name']]) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kopię zapasową?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash-alt"></i> Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Brak dostępnych kopii zapasowych. Kliknij przycisk powyżej, aby utworzyć nową kopię.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informacje o kopiach zapasowych</h2>
            
            <div class="text-sm text-gray-600">
                <p class="mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Kopie zapasowe zawierają kompletną kopię bazy danych oraz kluczowych plików systemu.
                </p>
                <p class="mb-2">
                    <i class="fas fa-clock mr-2"></i>
                    Zalecamy regularne tworzenie kopii zapasowych, szczególnie przed istotnymi aktualizacjami systemu.
                </p>
                <p>
                    <i class="fas fa-shield-alt mr-2"></i>
                    Kopie zapasowe są przechowywane w bezpiecznej lokalizacji na serwerze, ale zalecamy również pobieranie ich i przechowywanie w zewnętrznej lokalizacji.
                </p>
            </div>
        </div>
    </div>
</x-admin-layout> 