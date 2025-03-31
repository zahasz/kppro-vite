<x-admin-layout>
    <x-slot name="header">
        Tworzenie nowej subskrypcji użytkownika
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.subscriptions.store-user-subscription') }}">
                        @csrf

                        <!-- Użytkownik i plan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Użytkownik</label>
                                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="">Wybierz użytkownika</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select name="subscription_plan_id" id="subscription_plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="">Wybierz plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }} 
                                            data-price="{{ $plan->price }}" data-interval="{{ $plan->billing_period }}">
                                            {{ $plan->name }} ({{ number_format($plan->price, 2) }} zł)
                                        </option>
                                    @endforeach
                                </select>
                                @error('subscription_plan_id')
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
                                    <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subscription_type" class="block text-sm font-medium text-gray-700">Typ subskrypcji</label>
                                <select name="subscription_type" id="subscription_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                    <option value="manual" {{ old('subscription_type') == 'manual' ? 'selected' : '' }}>Ręczna</option>
                                    <option value="automatic" {{ old('subscription_type') == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
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
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktywna</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Oczekująca</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Anulowana</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Wygasła</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50" required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
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
                                <input type="date" name="next_payment_date" id="next_payment_date" value="{{ old('next_payment_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                <p class="mt-1 text-xs text-gray-500">Tylko dla subskrypcji automatycznych</p>
                                @error('next_payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <input type="text" name="payment_method" id="payment_method" value="{{ old('payment_method') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notatki -->
                        <div class="mb-6">
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700">Notatki</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-steel-blue-300 focus:ring focus:ring-steel-blue-200 focus:ring-opacity-50">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opcje dodatkowe -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="auto_renew" name="auto_renew" type="checkbox" value="1" {{ old('auto_renew') ? 'checked' : '' }} class="h-4 w-4 text-steel-blue-600 border-gray-300 rounded focus:ring-steel-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="auto_renew" class="font-medium text-gray-700">Automatyczne odnawianie</label>
                                    <p class="text-gray-500">Subskrypcja będzie odnawiana automatycznie</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="create_payment" name="create_payment" type="checkbox" value="1" {{ old('create_payment', true) ? 'checked' : '' }} class="h-4 w-4 text-steel-blue-600 border-gray-300 rounded focus:ring-steel-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="create_payment" class="font-medium text-gray-700">Utwórz fakturę</label>
                                    <p class="text-gray-500">Wygeneruj fakturę dla tej subskrypcji</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="send_notification" name="send_notification" type="checkbox" value="1" {{ old('send_notification', true) ? 'checked' : '' }} class="h-4 w-4 text-steel-blue-600 border-gray-300 rounded focus:ring-steel-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="send_notification" class="font-medium text-gray-700">Powiadom użytkownika</label>
                                    <p class="text-gray-500">Wyślij e-mail z informacją o subskrypcji</p>
                                </div>
                            </div>
                        </div>

                        <!-- Przyciski formularza -->
                        <div class="flex justify-end items-center space-x-3">
                            <a href="{{ route('admin.subscriptions.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                                Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-900 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Utwórz subskrypcję
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const planSelect = document.getElementById('subscription_plan_id');
            const priceInput = document.getElementById('price');
            const subscriptionTypeSelect = document.getElementById('subscription_type');
            const autoRenewCheckbox = document.getElementById('auto_renew');
            
            // Aktualizuj cenę gdy plan się zmienia
            planSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                priceInput.value = selectedOption.dataset.price || 0;
            });
            
            // Automatycznie zaznacz auto_renew dla automatycznych subskrypcji
            subscriptionTypeSelect.addEventListener('change', function() {
                if (this.value === 'automatic') {
                    autoRenewCheckbox.checked = true;
                }
            });
        });
    </script>
</x-admin-layout> 