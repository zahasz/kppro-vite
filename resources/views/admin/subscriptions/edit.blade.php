@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Edycja planu subskrypcyjnego</h2>
                <p class="mt-1 text-sm text-gray-600">Dostosuj ustawienia planu, limity i uprawnienia.</p>
            </div>
            <div>
                <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Powrót do listy planów
                </a>
            </div>
        </div>

        @include('admin.partials.success-alert')
        @include('admin.partials.error-alert')

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form action="{{ route('admin.subscriptions.update', $plan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dane podstawowe -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Dane podstawowe</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa planu</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $plan->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Nazwa planu" required />
                        </div>
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Kod planu</label>
                            <input type="text" id="code" name="code" value="{{ old('code', $plan->code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="np. basic, pro, enterprise" required />
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Krótki opis planu subskrypcyjnego">{{ old('description', $plan->description) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">zł</span>
                                </div>
                                <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $plan->price) }}" class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="0.00" required />
                            </div>
                        </div>
                        
                        <div>
                            <label for="billing_period" class="block text-sm font-medium text-gray-700">Okres rozliczeniowy</label>
                            <select id="billing_period" name="billing_period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                <option value="monthly" {{ old('billing_period', $plan->billing_period) == 'monthly' ? 'selected' : '' }}>Miesięczny</option>
                                <option value="yearly" {{ old('billing_period', $plan->billing_period) == 'yearly' ? 'selected' : '' }}>Roczny</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Limity i dodatkowe opcje -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Limity i opcje</h3>
                        
                        <div>
                            <label for="max_invoices" class="block text-sm font-medium text-gray-700">Limit faktur</label>
                            <input type="number" min="0" id="max_invoices" name="max_invoices" value="{{ old('max_invoices', $plan->max_invoices) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Limit faktur" />
                        </div>
                        
                        <div>
                            <label for="max_products" class="block text-sm font-medium text-gray-700">Limit produktów</label>
                            <input type="number" min="0" id="max_products" name="max_products" value="{{ old('max_products', $plan->max_products) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Limit produktów" />
                        </div>
                        
                        <div>
                            <label for="max_clients" class="block text-sm font-medium text-gray-700">Limit kontrahentów</label>
                            <input type="number" min="0" id="max_clients" name="max_clients" value="{{ old('max_clients', $plan->max_clients) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Limit kontrahentów" />
                        </div>
                        
                        <div>
                            <label for="trial_days" class="block text-sm font-medium text-gray-700">Dni okresu próbnego</label>
                            <input type="number" min="0" id="trial_days" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" placeholder="Liczba dni okresu próbnego" />
                        </div>
                        
                        <div class="mt-4">
                            <span class="text-sm font-medium text-gray-700">Widoczność planu:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center text-gray-700">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-steel-blue-600 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} />
                                    <span class="ml-2">Plan aktywny</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <span class="text-sm font-medium text-gray-700">Dostępny dla nowych użytkowników:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center text-gray-700">
                                    <input type="checkbox" id="is_public" name="is_public" value="1" class="rounded border-gray-300 text-steel-blue-600 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" {{ old('is_public', $plan->is_public) ? 'checked' : '' }} />
                                    <span class="ml-2">Dostępny publicznie</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Uprawnienia planu -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Uprawnienia planu</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissionsByCategory as $category => $permissions)
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-700 capitalize">{{ $category }}</h4>
                            
                            @foreach($permissions as $permission)
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" 
                                        class="rounded border-gray-300 text-steel-blue-600 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50"
                                        {{ in_array($permission->id, $planPermissions) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="permission_{{ $permission->id }}" class="font-medium text-gray-700">{{ $permission->name }}</label>
                                    <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-800 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Zaktualizuj plan
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Anuluj
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 