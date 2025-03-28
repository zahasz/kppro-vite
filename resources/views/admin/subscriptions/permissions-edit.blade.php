<x-admin-layout>
    <x-slot name="header">
        Edycja uprawnienia subskrypcji
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-5">
                        <h2 class="text-lg font-medium text-gray-900">
                            Edycja uprawnienia
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Edytuj szczegóły uprawnienia i określ w jakich planach subskrypcji ma ono być dostępne.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.subscriptions.permissions.update', $permission) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa uprawnienia</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Nazwa wyświetlana dla użytkowników">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kod uprawnienia</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $permission->code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Unikalny identyfikator uprawnienia">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Krótki opis tego uprawnienia">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="finance" {{ old('category', $permission->category) == 'finance' ? 'selected' : '' }}>Finanse</option>
                                <option value="warehouse" {{ old('category', $permission->category) == 'warehouse' ? 'selected' : '' }}>Magazyn</option>
                                <option value="documents" {{ old('category', $permission->category) == 'documents' ? 'selected' : '' }}>Dokumenty</option>
                                <option value="integrations" {{ old('category', $permission->category) == 'integrations' ? 'selected' : '' }}>Integracje</option>
                                <option value="reports" {{ old('category', $permission->category) == 'reports' ? 'selected' : '' }}>Raporty</option>
                                <option value="users" {{ old('category', $permission->category) == 'users' ? 'selected' : '' }}>Użytkownicy</option>
                                <option value="other" {{ old('category', $permission->category) == 'other' ? 'selected' : '' }}>Inne</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <span class="block text-sm font-medium text-gray-700 mb-3">Dostępność w planach subskrypcji</span>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="select-all" class="ml-2 block text-sm font-medium text-gray-700">Zaznacz wszystkie</label>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach($subscriptionPlans as $plan)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="plans[]" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" 
                                            @if(is_array(old('plans')) && in_array($plan->id, old('plans'))) 
                                                checked
                                            @elseif(old('plans') === null && $permission->plans->contains($plan->id))
                                                checked
                                            @endif
                                            class="plan-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="plan-{{ $plan->id }}" class="ml-2 block text-sm text-gray-700">
                                            {{ $plan->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('plans')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="feature_flag" class="block text-sm font-medium text-gray-700 mb-1">Feature flag (opcjonalnie)</label>
                            <input type="text" name="feature_flag" id="feature_flag" value="{{ old('feature_flag', $permission->feature_flag) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Identyfikator w systemie funkcji flagowych (jeśli używany)">
                            <p class="mt-1 text-xs text-gray-500">Jeśli używasz systemu feature flags, możesz podać tutaj identyfikator, który będzie włączał/wyłączał tę funkcję.</p>
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('admin.subscriptions.permissions') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const planCheckboxes = document.querySelectorAll('.plan-checkbox');

            // Sprawdź początkowy stan
            updateSelectAllState();

            selectAllCheckbox.addEventListener('change', function() {
                planCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Aktualizuj "Zaznacz wszystkie" gdy poszczególne plany są kliknięte
            planCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                });
            });

            function updateSelectAllState() {
                const allChecked = Array.from(planCheckboxes).every(c => c.checked);
                const someChecked = Array.from(planCheckboxes).some(c => c.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    </script>
</x-admin-layout> 