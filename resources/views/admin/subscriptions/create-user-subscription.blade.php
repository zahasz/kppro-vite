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
                                <div class="mt-1 relative">
                                    <input type="text" name="user_search" id="user_search" placeholder="Wyszukaj użytkownika po nazwie lub emailu" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <input type="hidden" name="user_id" id="user_id">
                                </div>
                                <div id="user_suggestions" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                    <!-- Przykładowe dane do wyszukiwania użytkowników -->
                                    <div data-id="1" class="user-item cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <span class="ml-3 block font-medium truncate">Jan Kowalski (jan.kowalski@example.com)</span>
                                        </div>
                                    </div>
                                    <div data-id="2" class="user-item cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <span class="ml-3 block font-medium truncate">Anna Nowak (anna.nowak@example.com)</span>
                                        </div>
                                    </div>
                                    <div data-id="3" class="user-item cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <span class="ml-3 block font-medium truncate">Piotr Wiśniewski (piotr.wisniewski@example.com)</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const userSearch = document.getElementById('user_search');
                                        const userSuggestions = document.getElementById('user_suggestions');
                                        const userIdInput = document.getElementById('user_id');
                                        const userItems = document.querySelectorAll('.user-item');
                                        
                                        userSearch.addEventListener('focus', function() {
                                            userSuggestions.classList.remove('hidden');
                                        });
                                        
                                        userSearch.addEventListener('input', function() {
                                            userSuggestions.classList.remove('hidden');
                                            // Tutaj normalnie byłoby API call do wyszukiwania użytkowników
                                        });
                                        
                                        document.addEventListener('click', function(e) {
                                            if (!userSearch.contains(e.target) && !userSuggestions.contains(e.target)) {
                                                userSuggestions.classList.add('hidden');
                                            }
                                        });
                                        
                                        userItems.forEach(item => {
                                            item.addEventListener('click', function() {
                                                userIdInput.value = this.dataset.id;
                                                userSearch.value = this.querySelector('span').textContent;
                                                userSuggestions.classList.add('hidden');
                                            });
                                        });
                                    });
                                </script>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="subscription_plan" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select id="subscription_plan" name="subscription_plan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="free">Darmowy (0 PLN)</option>
                                    <option value="standard">Standard (49 PLN / miesiąc)</option>
                                    <option value="premium">Premium (99 PLN / miesiąc)</option>
                                    <option value="premium_yearly">Premium Roczny (999 PLN / rok)</option>
                                </select>
                                @error('subscription_plan')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="active">Aktywna</option>
                                    <option value="pending" selected>Oczekująca na płatność</option>
                                    <option value="trial">Trial</option>
                                </select>
                                @error('status')
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
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
                                <input type="date" name="end_date" id="end_date" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla bezterminowej subskrypcji</p>
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="card">Karta płatnicza</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Przelew bankowy</option>
                                    <option value="none">Brak (darmowy plan)</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_details" class="block text-sm font-medium text-gray-700">Szczegóły płatności</label>
                                <input type="text" name="payment_details" id="payment_details" placeholder="np. ostatnie 4 cyfry karty, adres email PayPal" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('payment_details')
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
</x-admin-layout> 