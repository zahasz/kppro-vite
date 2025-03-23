<x-admin-layout>
    <x-slot name="header">
        {{ isset($plan) ? 'Edycja planu subskrypcji' : 'Dodaj nowy plan subskrypcji' }}
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ isset($plan) ? 'Edycja planu: ' . $plan->name : 'Nowy plan subskrypcji' }}
            </h2>
        </div>

        <form method="POST" action="{{ isset($plan) ? route('admin.subscriptions.update', $plan->id) : route('admin.subscriptions.store') }}" class="p-6">
            @csrf
            @if(isset($plan))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nazwa planu -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nazwa planu</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->name ?? old('name') }}" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kod planu -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kod planu (unikalny identyfikator)</label>
                    <input type="text" name="code" id="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->code ?? old('code') }}" required>
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opis planu -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Opis planu</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $plan->description ?? old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cena -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="price" id="price" step="0.01" class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->price ?? old('price') }}" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">PLN</span>
                        </div>
                    </div>
                    @error('price')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Okres rozliczeniowy -->
                <div>
                    <label for="billing_period" class="block text-sm font-medium text-gray-700">Okres rozliczeniowy</label>
                    <select name="billing_period" id="billing_period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="monthly" {{ (isset($plan) && $plan->billing_period == 'monthly') ? 'selected' : '' }}>Miesięczny</option>
                        <option value="quarterly" {{ (isset($plan) && $plan->billing_period == 'quarterly') ? 'selected' : '' }}>Kwartalny</option>
                        <option value="yearly" {{ (isset($plan) && $plan->billing_period == 'yearly') ? 'selected' : '' }}>Roczny</option>
                        <option value="lifetime" {{ (isset($plan) && $plan->billing_period == 'lifetime') ? 'selected' : '' }}>Bezterminowy</option>
                    </select>
                    @error('billing_period')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Limity i funkcje -->
                <div class="md:col-span-2 border-t pt-4 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Limity i funkcje</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="max_invoices" class="block text-sm font-medium text-gray-700">Maksymalna liczba faktur miesięcznie</label>
                            <input type="number" name="max_invoices" id="max_invoices" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->max_invoices ?? old('max_invoices') }}">
                            <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla nieograniczonej ilości</p>
                        </div>
                        
                        <div>
                            <label for="max_products" class="block text-sm font-medium text-gray-700">Maksymalna liczba produktów</label>
                            <input type="number" name="max_products" id="max_products" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->max_products ?? old('max_products') }}">
                            <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla nieograniczonej ilości</p>
                        </div>
                        
                        <div>
                            <label for="max_contractors" class="block text-sm font-medium text-gray-700">Maksymalna liczba kontrahentów</label>
                            <input type="number" name="max_contractors" id="max_contractors" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->max_contractors ?? old('max_contractors') }}">
                            <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla nieograniczonej ilości</p>
                        </div>
                        
                        <div>
                            <label for="features" class="block text-sm font-medium text-gray-700">Dostępne funkcje</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_finances" name="features[]" type="checkbox" value="finances" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('finances', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_finances" class="font-medium text-gray-700">Moduł finansów</label>
                                        <p class="text-gray-500">Analityka finansowa, raporty, budżety</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_warehouse" name="features[]" type="checkbox" value="warehouse" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('warehouse', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_warehouse" class="font-medium text-gray-700">Moduł magazynowy</label>
                                        <p class="text-gray-500">Zarządzanie magazynem i stanami magazynowymi</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_contracts" name="features[]" type="checkbox" value="contracts" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('contracts', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_contracts" class="font-medium text-gray-700">Moduł umów</label>
                                        <p class="text-gray-500">Zarządzanie umowami, szablony, przypomnienia</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_estimates" name="features[]" type="checkbox" value="estimates" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('estimates', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_estimates" class="font-medium text-gray-700">Moduł kosztorysów</label>
                                        <p class="text-gray-500">Tworzenie i zarządzanie kosztorysami</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_reports" name="features[]" type="checkbox" value="reports" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('reports', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_reports" class="font-medium text-gray-700">Zaawansowane raporty</label>
                                        <p class="text-gray-500">Eksport raportów do wielu formatów, niestandardowe raporty</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="feature_api" name="features[]" type="checkbox" value="api" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ (isset($plan) && in_array('api', $plan->features ?? [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="feature_api" class="font-medium text-gray-700">Dostęp do API</label>
                                        <p class="text-gray-500">Możliwość integracji z innymi systemami</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1" {{ (isset($plan) && $plan->is_active) ? 'selected' : '' }}>Aktywny</option>
                        <option value="0" {{ (isset($plan) && !$plan->is_active) ? 'selected' : '' }}>Nieaktywny</option>
                    </select>
                </div>
                
                <!-- Kolejność wyświetlania -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Kolejność wyświetlania</label>
                    <input type="number" name="order" id="order" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $plan->order ?? old('order', 0) }}">
                    <p class="mt-1 text-xs text-gray-500">Niższe wartości będą wyświetlane jako pierwsze</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.subscriptions.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Anuluj
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ isset($plan) ? 'Zaktualizuj plan' : 'Utwórz plan' }}
                </button>
            </div>
        </form>
    </div>
</x-admin-layout> 