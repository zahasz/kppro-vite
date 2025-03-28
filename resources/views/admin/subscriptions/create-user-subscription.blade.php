<x-admin-layout>
    <x-slot name="header">
        Przypisz subskrypcję do użytkownika
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Przypisz subskrypcję do użytkownika</h2>
                    
                    <form action="{{ route('admin.subscriptions.store-user-subscription') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Użytkownik</label>
                                <div class="mt-1">
                                    <select id="user_id" name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Wybierz użytkownika</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="plan_id" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select id="plan_id" name="plan_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Wybierz plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-interval="{{ $plan->interval }}">
                                            {{ $plan->name }} ({{ number_format($plan->price, 2) }} PLN / {{ $plan->interval == 'monthly' ? 'miesiąc' : ($plan->interval == 'annually' ? 'rok' : $plan->interval) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="active">Aktywna</option>
                                    <option value="pending" selected>Oczekująca na płatność</option>
                                    <option value="inactive">Nieaktywna</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="subscription_type" class="block text-sm font-medium text-gray-700">Typ subskrypcji</label>
                                <select id="subscription_type" name="subscription_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="manual">Ręczna (bez automatycznego odnowienia)</option>
                                    <option value="automatic">Automatyczna (z automatycznym odnowieniem)</option>
                                </select>
                                @error('subscription_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div id="renewal_options" class="sm:col-span-3 hidden">
                                <label for="renewal_status" class="block text-sm font-medium text-gray-700">Status odnowienia</label>
                                <select id="renewal_status" name="renewal_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="enabled" selected>Włączone</option>
                                    <option value="disabled">Wyłączone</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Określa, czy subskrypcja będzie odnawiana automatycznie</p>
                                @error('renewal_status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                                <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="trial_ends_at" class="block text-sm font-medium text-gray-700">Data zakończenia okresu próbnego</label>
                                <input type="date" name="trial_ends_at" id="trial_ends_at" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Pozostaw puste jeśli nie dotyczy</p>
                                @error('trial_ends_at')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="price" id="price" step="0.01" min="0" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">PLN</span>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="card">Karta płatnicza</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Przelew bankowy</option>
                                    <option value="cash">Gotówka</option>
                                    <option value="free">Bezpłatnie</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_id" class="block text-sm font-medium text-gray-700">ID płatności</label>
                                <input type="text" name="payment_id" id="payment_id" placeholder="np. ID transakcji, nr faktury" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('payment_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-6 mt-4">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="send_notification" name="send_notification" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="send_notification" class="font-medium text-gray-700">Powiadom użytkownika o przypisaniu subskrypcji</label>
                                        <p class="text-gray-500">Wyślij e-mail z informacją o przypisaniu subskrypcji</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700">Uwagi administracyjne</label>
                                <textarea id="admin_notes" name="admin_notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Opcjonalne notatki widoczne tylko dla administratorów"></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex justify-end">
                                <a href="{{ route('admin.subscriptions.users') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                    Anuluj
                                </a>
                                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Przypisz subskrypcję
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subscriptionTypeSelect = document.getElementById('subscription_type');
            const renewalOptionsDiv = document.getElementById('renewal_options');
            const planSelect = document.getElementById('plan_id');
            const priceInput = document.getElementById('price');
            
            // Pokaż/ukryj opcje odnowienia w zależności od typu subskrypcji
            subscriptionTypeSelect.addEventListener('change', function() {
                if (this.value === 'automatic') {
                    renewalOptionsDiv.classList.remove('hidden');
                } else {
                    renewalOptionsDiv.classList.add('hidden');
                }
            });
            
            // Ustawienie ceny na podstawie wybranego planu
            planSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value !== '') {
                    priceInput.value = selectedOption.dataset.price;
                } else {
                    priceInput.value = '';
                }
            });
            
            // Inicjalne ustawienie widoczności opcji odnowienia
            if (subscriptionTypeSelect.value === 'automatic') {
                renewalOptionsDiv.classList.remove('hidden');
            }
        });
    </script>
    @endpush
</x-admin-layout> 