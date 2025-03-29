<x-admin-layout>
    <x-slot name="header">
        Edycja subskrypcji użytkownika
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.subscriptions.update-user-subscription', $subscription->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Użytkownik i plan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Użytkownik</label>
                                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="plan_id" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select name="plan_id" id="plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }} 
                                            data-price="{{ $plan->price }}" data-interval="{{ $plan->interval }}">
                                            {{ $plan->name }} ({{ number_format($plan->price, 2) }} zł)
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cena i typ subskrypcji -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">zł</span>
                                    </div>
                                    <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price', $subscription->price) }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subscription_type" class="block text-sm font-medium text-gray-700">Typ subskrypcji</label>
                                <select name="subscription_type" id="subscription_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="manual" {{ old('subscription_type', $subscription->subscription_type) == 'manual' ? 'selected' : '' }}>Ręczna</option>
                                    <option value="automatic" {{ old('subscription_type', $subscription->subscription_type) == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
                                </select>
                                @error('subscription_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status i daty -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>Aktywna</option>
                                    <option value="pending" {{ old('status', $subscription->status) == 'pending' ? 'selected' : '' }}>Oczekująca</option>
                                    <option value="cancelled" {{ old('status', $subscription->status) == 'cancelled' ? 'selected' : '' }}>Anulowana</option>
                                    <option value="expired" {{ old('status', $subscription->status) == 'expired' ? 'selected' : '' }}>Wygasła</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $subscription->start_date ? $subscription->start_date->format('Y-m-d') : date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $subscription->end_date ? $subscription->end_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                <p class="mt-1 text-xs text-gray-500">Opcjonalne dla subskrypcji bezterminowych</p>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Płatności -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="next_payment_date" class="block text-sm font-medium text-gray-700">Następna płatność</label>
                                <input type="date" name="next_payment_date" id="next_payment_date" value="{{ old('next_payment_date', $subscription->next_payment_date ? $subscription->next_payment_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                <p class="mt-1 text-xs text-gray-500">Tylko dla subskrypcji automatycznych</p>
                                @error('next_payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <input type="text" name="payment_method" id="payment_method" value="{{ old('payment_method', $subscription->payment_method) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notatki -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notatki</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">{{ old('notes', $subscription->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Przyciski formularza -->
                        <div class="flex justify-end items-center space-x-3">
                            <a href="{{ route('admin.subscriptions.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                                Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-900 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
            const planSelect = document.getElementById('plan_id');
            const priceInput = document.getElementById('price');
            
            // Aktualizuj cenę gdy plan się zmienia
            planSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                priceInput.value = selectedOption.dataset.price || 0;
            });
        });
    </script>
</x-admin-layout> 