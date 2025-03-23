<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Zarządzanie kontami bankowymi') }}
            </h2>
            <a href="{{ route('profile.edit') }}#dane-bankowe" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition duration-150 ease-in-out">
                {{ __('Powrót do profilu') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(!$user->companyProfile)
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                            <p>{{ __('Musisz najpierw utworzyć profil firmy, aby móc zarządzać kontami bankowymi.') }}</p>
                            <p class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">
                                    {{ __('Przejdź do profilu, aby utworzyć profil firmy') }}
                                </a>
                            </p>
                        </div>
                    @else
                        <!-- Istniejące konta bankowe -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Twoje konta bankowe') }}</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nazwa konta') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Numer konta') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nazwa banku') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('SWIFT') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Akcje') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($bankAccounts as $bankAccount)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $bankAccount->account_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $bankAccount->account_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $bankAccount->bank_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $bankAccount->swift ?: '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($bankAccount->is_default)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ __('Domyślne') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            {{ __('Standardowe') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                    @if(!$bankAccount->is_default)
                                                        <form method="post" action="{{ route('bank-accounts.set-default', $bankAccount) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition duration-150 ease-in-out">
                                                                {{ __('Ustaw domyślne') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <button type="button" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none transition duration-150 ease-in-out edit-bank-account" 
                                                           data-id="{{ $bankAccount->id }}"
                                                           data-name="{{ $bankAccount->account_name }}"
                                                           data-number="{{ $bankAccount->account_number }}"
                                                           data-bank="{{ $bankAccount->bank_name }}"
                                                           data-swift="{{ $bankAccount->swift }}"
                                                           data-default="{{ $bankAccount->is_default ? '1' : '0' }}">
                                                        {{ __('Edytuj') }}
                                                    </button>
                                                    
                                                    <form method="post" action="{{ route('bank-accounts.destroy', $bankAccount) }}" class="inline">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition duration-150 ease-in-out" 
                                                              onclick="return confirm('{{ __('Czy na pewno chcesz usunąć to konto bankowe?') }}')">
                                                            {{ __('Usuń') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">
                                                    {{ __('Brak dodanych kont bankowych. Dodaj pierwsze konto używając formularza poniżej.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Formularz dodawania konta bankowego -->
                        <div id="add-bank-account-form" class="border rounded-md p-6 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Dodaj nowe konto bankowe') }}</h3>
                            
                            <form method="post" action="{{ route('bank-accounts.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @csrf
                                
                                <!-- Nazwa konta -->
                                <div>
                                    <x-input-label for="account_name" :value="__('Nazwa konta')" />
                                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                                </div>
                                
                                <!-- Numer konta -->
                                <div>
                                    <x-input-label for="account_number" :value="__('Numer konta')" />
                                    <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                                </div>
                                
                                <!-- Nazwa banku -->
                                <div>
                                    <x-input-label for="bank_name" :value="__('Nazwa banku')" />
                                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                                </div>
                                
                                <!-- SWIFT -->
                                <div>
                                    <x-input-label for="swift" :value="__('Kod SWIFT')" />
                                    <x-text-input id="swift" name="swift" type="text" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('swift')" class="mt-2" />
                                </div>
                                
                                <!-- Ustawienie jako domyślne -->
                                <div class="md:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ __('Ustaw jako domyślne konto bankowe') }}</span>
                                    </label>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <x-primary-button>{{ __('Dodaj konto bankowe') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Formularz edycji konta bankowego - ukryty domyślnie -->
                        <div id="edit-bank-account-form" class="border rounded-md p-6 bg-yellow-50 mt-6 hidden">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Edytuj konto bankowe') }}</h3>
                            
                            <form method="post" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6" id="edit-bank-account-form-element">
                                @csrf
                                @method('put')
                                
                                <!-- Nazwa konta -->
                                <div>
                                    <x-input-label for="edit_account_name" :value="__('Nazwa konta')" />
                                    <x-text-input id="edit_account_name" name="account_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                                </div>
                                
                                <!-- Numer konta -->
                                <div>
                                    <x-input-label for="edit_account_number" :value="__('Numer konta')" />
                                    <x-text-input id="edit_account_number" name="account_number" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                                </div>
                                
                                <!-- Nazwa banku -->
                                <div>
                                    <x-input-label for="edit_bank_name" :value="__('Nazwa banku')" />
                                    <x-text-input id="edit_bank_name" name="bank_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                                </div>
                                
                                <!-- SWIFT -->
                                <div>
                                    <x-input-label for="edit_swift" :value="__('Kod SWIFT')" />
                                    <x-text-input id="edit_swift" name="swift" type="text" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('swift')" class="mt-2" />
                                </div>
                                
                                <!-- Ustawienie jako domyślne -->
                                <div class="md:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_default" id="edit_is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ __('Ustaw jako domyślne konto bankowe') }}</span>
                                    </label>
                                </div>
                                
                                <div class="md:col-span-2 flex justify-between">
                                    <x-primary-button>{{ __('Zapisz zmiany') }}</x-primary-button>
                                    <button type="button" id="cancel-edit" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Anuluj') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obsługa przycisków edycji
            const editButtons = document.querySelectorAll('.edit-bank-account');
            const editForm = document.getElementById('edit-bank-account-form');
            const editFormElement = document.getElementById('edit-bank-account-form-element');
            const cancelEditButton = document.getElementById('cancel-edit');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const number = this.dataset.number;
                    const bank = this.dataset.bank;
                    const swift = this.dataset.swift;
                    const isDefault = this.dataset.default === '1';
                    
                    // Ustawienie wartości w formularzu
                    document.getElementById('edit_account_name').value = name;
                    document.getElementById('edit_account_number').value = number;
                    document.getElementById('edit_bank_name').value = bank;
                    document.getElementById('edit_swift').value = swift;
                    document.getElementById('edit_is_default').checked = isDefault;
                    
                    // Ustawienie akcji formularza
                    editFormElement.action = `/bank-accounts/${id}`;
                    
                    // Pokazanie formularza edycji
                    editForm.classList.remove('hidden');
                    
                    // Przewinięcie do formularza
                    editForm.scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            // Obsługa przycisku anulowania edycji
            if (cancelEditButton) {
                cancelEditButton.addEventListener('click', function() {
                    editForm.classList.add('hidden');
                });
            }
        });
    </script>
    @endpush
</x-app-layout> 