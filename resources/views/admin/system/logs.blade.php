<x-admin-layout>
    <x-slot name="header">
        Logi systemowe
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Logi systemu</h2>

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6 flex justify-between items-center">
                <div>
                    <span class="text-sm text-gray-600">Wyświetlanie ostatnich 100 wpisów</span>
                </div>
                <div>
                    <form action="{{ route('admin.system.logs.clear') }}" method="POST" class="inline-block" onsubmit="return confirm('Czy na pewno chcesz wyczyścić logi? Ta operacja jest nieodwracalna.')">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                            <i class="fas fa-trash-alt mr-2"></i> Wyczyść logi
                        </button>
                    </form>
                    <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded ml-2">
                        <i class="fas fa-print mr-2"></i> Drukuj
                    </button>
                </div>
            </div>

            @if(count($logs) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data i czas
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Poziom
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Wiadomość
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontekst
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($logs as $log)
                                <tr class="{{ $log['level'] === 'error' ? 'bg-red-50' : ($log['level'] === 'warning' ? 'bg-yellow-50' : '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log['level'] === 'error')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Błąd
                                            </span>
                                        @elseif($log['level'] === 'warning')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Ostrzeżenie
                                            </span>
                                        @elseif($log['level'] === 'info')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Informacja
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $log['level'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{ $log['message'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if(isset($log['context']) && !empty($log['context']))
                                            <button 
                                                type="button" 
                                                class="text-blue-600 hover:text-blue-900"
                                                onclick="document.getElementById('context-{{ $loop->index }}').classList.toggle('hidden')">
                                                Pokaż szczegóły
                                            </button>
                                            <div id="context-{{ $loop->index }}" class="hidden mt-2 bg-gray-50 p-2 rounded text-xs">
                                                <pre class="whitespace-pre-wrap">{{ json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Brak</span>
                                        @endif
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
                                Brak dostępnych logów systemowych.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informacje o logach</h2>
            
            <div class="text-sm text-gray-600">
                <p class="mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Logi systemowe zawierają informacje o działaniu aplikacji, błędach i ostrzeżeniach.
                </p>
                <p class="mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Poziomy logów: <span class="text-blue-600">Informacja</span> (standardowe zdarzenia), 
                    <span class="text-yellow-600">Ostrzeżenie</span> (potencjalne problemy), 
                    <span class="text-red-600">Błąd</span> (krytyczne problemy).
                </p>
                <p>
                    <i class="fas fa-clock mr-2"></i>
                    System automatycznie rotuje logi starsze niż 30 dni.
                </p>
            </div>
        </div>
    </div>
</x-admin-layout> 