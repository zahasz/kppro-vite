<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edytuj bramkę płatności
            </h2>
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                <i class="fas fa-arrow-left mr-2"></i> Powrót do listy
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Wystąpił błąd!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.payments.update', $gateway->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Informacje podstawowe</h3>
                            <p class="text-sm text-gray-600 mb-4">Edytuj podstawowe informacje o bramce płatności.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa bramki <span class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $gateway->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Nazwa wyświetlana użytkownikom, np. "Płatność kartą"</p>
                                </div>
                                
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kod bramki <span class="text-red-500">*</span></label>
                                    <input type="text" id="code" name="code" value="{{ old('code', $gateway->code) }}" required pattern="[a-z0-9_]+" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Unikalny kod identyfikujący bramkę, np. "card_payment" (tylko małe litery, cyfry i podkreślenia)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="class_name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa klasy <span class="text-red-500">*</span></label>
                                    <input type="text" id="class_name" name="class_name" value="{{ old('class_name', $gateway->class_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Pełna nazwa klasy implementującej bramkę, np. "App\Gateways\CardPaymentGateway"</p>
                                </div>
                                
                                <div>
                                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Kolejność wyświetlania</label>
                                    <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $gateway->display_order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Określa kolejność wyświetlania bramki na liście (niższe liczby są wyświetlane wcześniej)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('description', $gateway->description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Krótki opis bramki płatności widoczny dla użytkowników</p>
                        </div>
                        
                        <div class="mb-6">
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            <div class="flex items-center space-x-4">
                                @if ($gateway->logo_path)
                                <div class="flex items-center">
                                    <img src="{{ asset('storage/' . $gateway->logo_path) }}" alt="{{ $gateway->name }}" class="h-10 mr-3">
                                    <span class="text-sm text-gray-600">Obecne logo</span>
                                </div>
                                @else
                                <span class="text-sm text-gray-600">Brak logo</span>
                                @endif
                            </div>
                            <input type="file" id="logo" name="logo" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Wgraj nowe logo bramki płatności (opcjonalnie, maksymalny rozmiar: 1MB)</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfiguracja bramki</h3>
                            <p class="text-sm text-gray-600 mb-4">Określ dodatkowe ustawienia bramki płatności.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $gateway->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktywna</label>
                                    <p class="ml-6 text-xs text-gray-500">Określa, czy bramka jest aktywna i dostępna dla użytkowników</p>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="test_mode" name="test_mode" value="1" {{ old('test_mode', $gateway->test_mode) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="test_mode" class="ml-2 block text-sm text-gray-900">Tryb testowy</label>
                                    <p class="ml-6 text-xs text-gray-500">Określa, czy bramka działa w trybie testowym (bez rzeczywistych transakcji)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Dane konfiguracyjne</h3>
                            <p class="text-sm text-gray-600 mb-4">Edytuj dane konfiguracyjne specyficzne dla wybranej bramki płatności.</p>
                            
                            <div id="config-fields">
                                @if ($gateway->config && is_array($gateway->config) && count($gateway->config) > 0)
                                    @foreach ($gateway->config as $key => $value)
                                    <div class="mb-4 config-field-row">
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Klucz</label>
                                                <input type="text" name="config[keys][]" value="{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Wartość</label>
                                                <div class="flex">
                                                    <input type="text" name="config[values][]" value="{{ $value }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <button type="button" class="remove-field ml-2 mt-1 text-red-500" title="Usuń pole">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="mb-4 config-field-row">
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Klucz</label>
                                                <input type="text" name="config[keys][]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Wartość</label>
                                                <div class="flex">
                                                    <input type="text" name="config[values][]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <button type="button" class="remove-field ml-2 mt-1 text-red-500" title="Usuń pole">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <button type="button" id="add-config-field" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Dodaj pole konfiguracyjne
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                                <i class="fas fa-times mr-2"></i> Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <i class="fas fa-save mr-2"></i> Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dynamiczne dodawanie pól konfiguracyjnych
            const addButton = document.getElementById('add-config-field');
            const configFields = document.getElementById('config-fields');
            
            addButton.addEventListener('click', function() {
                const fieldRow = document.createElement('div');
                fieldRow.className = 'mb-4 config-field-row';
                fieldRow.innerHTML = `
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Klucz</label>
                            <input type="text" name="config[keys][]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wartość</label>
                            <div class="flex">
                                <input type="text" name="config[values][]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <button type="button" class="remove-field ml-2 mt-1 text-red-500" title="Usuń pole">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                configFields.appendChild(fieldRow);
                
                // Obsługa usuwania pola
                fieldRow.querySelector('.remove-field').addEventListener('click', function() {
                    fieldRow.remove();
                });
            });
            
            // Obsługa usuwania istniejących pól
            document.querySelectorAll('.remove-field').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.config-field-row').remove();
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 