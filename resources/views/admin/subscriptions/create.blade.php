<x-admin-layout>
    <x-slot name="header">
        Tworzenie nowego planu subskrypcji
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.subscriptions.store') }}">
                        @csrf

                        <!-- Nazwa planu -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa planu</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opis planu -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Opis planu</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cena i okres -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">zł</span>
                                    </div>
                                    <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="interval" class="block text-sm font-medium text-gray-700">Okres rozliczeniowy</label>
                                <select name="interval" id="interval" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="monthly" {{ old('interval') == 'monthly' ? 'selected' : '' }}>Miesięczny</option>
                                    <option value="quarterly" {{ old('interval') == 'quarterly' ? 'selected' : '' }}>Kwartalny</option>
                                    <option value="yearly" {{ old('interval') == 'yearly' ? 'selected' : '' }}>Roczny</option>
                                    <option value="lifetime" {{ old('interval') == 'lifetime' ? 'selected' : '' }}>Bezterminowy</option>
                                </select>
                                @error('interval')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Typ subskrypcji i okres próbny -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="subscription_type" class="block text-sm font-medium text-gray-700">Typ subskrypcji</label>
                                <select name="subscription_type" id="subscription_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="manual" {{ old('subscription_type') == 'manual' ? 'selected' : '' }}>Ręczna</option>
                                    <option value="automatic" {{ old('subscription_type') == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
                                    <option value="both" {{ old('subscription_type') == 'both' ? 'selected' : '' }}>Ręczna i automatyczna</option>
                                </select>
                                @error('subscription_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="trial_period_days" class="block text-sm font-medium text-gray-700">Okres próbny (dni)</label>
                                <input type="number" name="trial_period_days" id="trial_period_days" min="0" value="{{ old('trial_period_days', 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                <p class="mt-1 text-xs text-gray-500">Ustaw 0, aby wyłączyć okres próbny</p>
                                @error('trial_period_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Funkcje planu -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Funkcje planu</label>
                            <div class="space-y-2" id="features-container">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="features[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="np. Nieograniczona liczba projektów">
                                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500" onclick="addFeatureField()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Dodaj funkcję
                            </button>
                        </div>

                        <!-- Status planu -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-steel-blue-600 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" {{ old('is_active') ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">Plan aktywny</label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Nieaktywne plany nie będą widoczne dla użytkowników</p>
                        </div>

                        <!-- Przyciski formularza -->
                        <div class="flex justify-end items-center space-x-3">
                            <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                                Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-900 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Zapisz plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addFeatureField() {
            const container = document.getElementById('features-container');
            const newField = document.createElement('div');
            newField.className = 'flex items-center space-x-2';
            newField.innerHTML = `
                <input type="text" name="features[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="np. Nieograniczona liczba projektów">
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            container.appendChild(newField);
        }
    </script>
</x-admin-layout> 