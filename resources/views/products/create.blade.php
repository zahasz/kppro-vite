<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dodaj nowy produkt') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <!-- Nazwa -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nazwa')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Opis -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Opis')" />
                            <textarea id="description" name="description" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Jednostka -->
                        <div class="mb-4">
                            <x-input-label for="unit" :value="__('Jednostka')" />
                            <x-text-input id="unit" class="block mt-1 w-full" type="text" name="unit" :value="old('unit')" required />
                            <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                        </div>

                        <!-- Cena jednostkowa -->
                        <div class="mb-4">
                            <x-input-label for="unit_price" :value="__('Cena jednostkowa')" />
                            <x-text-input id="unit_price" class="block mt-1 w-full" type="number" step="0.01" name="unit_price" :value="old('unit_price')" required />
                            <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                        </div>

                        <!-- Stawka VAT -->
                        <div class="mb-4">
                            <x-input-label for="tax_rate" :value="__('Stawka VAT (%)')" />
                            <x-text-input id="tax_rate" class="block mt-1 w-full" type="number" step="0.01" name="tax_rate" :value="old('tax_rate', 23)" required />
                            <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktywny</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nieaktywny</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">
                                {{ __('Anuluj') }}
                            </a>
                            <x-primary-button>
                                {{ __('Zapisz') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 