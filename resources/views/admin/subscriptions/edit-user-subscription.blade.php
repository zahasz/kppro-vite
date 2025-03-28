<x-admin-layout>
    <x-slot name="header">
        Edycja subskrypcji użytkownika
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edycja subskrypcji użytkownika</h2>
                    
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700">
                                    Edytujesz subskrypcję dla użytkownika <span class="font-bold">{{ $subscription->user->name }}</span> ({{ $subscription->user->email }})
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.subscriptions.update-user-subscription', $subscription->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Użytkownik</label>
                                <select id="user_id" name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $subscription->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="plan_id" class="block text-sm font-medium text-gray-700">Plan subskrypcji</label>
                                <select id="plan_id" name="plan_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-interval="{{ $plan->interval }}" {{ $subscription->plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} ({{ number_format($plan->price, 2) }} PLN / {{ $plan->interval == 'monthly' ? 'miesiąc' : ($plan->interval == 'annually' ? 'rok' : $plan->interval) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="subscription_type" class="block text-sm font-medium text-gray-700">Typ subskrypcji</label>
                                <select id="subscription_type" name="subscription_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="manual" {{ $subscription->subscription_type == 'manual' ? 'selected' : '' }}>Ręczna (bez automatycznego odnowienia)</option>
                                    <option value="automatic" {{ $subscription->subscription_type == 'automatic' ? 'selected' : '' }}>Automatyczna (z automatycznym odnowieniem)</option>
                                </select>
                                @error('subscription_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="renewal_options" class="sm:col-span-3 {{ $subscription->subscription_type == 'automatic' ? '' : 'hidden' }}">
                                <label for="renewal_status" class="block text-sm font-medium text-gray-700">Status odnowienia</label>
                                <select id="renewal_status" name="renewal_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="enabled" {{ $subscription->renewal_status == 'enabled' ? 'selected' : '' }}>Włączone</option>
                                    <option value="disabled" {{ $subscription->renewal_status == 'disabled' ? 'selected' : '' }}>Wyłączone</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Określa, czy subskrypcja będzie odnawiana automatycznie</p>
                                @error('renewal_status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>Aktywna</option>
                                    <option value="pending" {{ $subscription->status == 'pending' ? 'selected' : '' }}>Oczekująca na płatność</option>
                                    <option value="inactive" {{ $subscription->status == 'inactive' ? 'selected' : '' }}>Nieaktywna</option>
                                    <option value="cancelled" {{ $subscription->status == 'cancelled' ? 'selected' : '' }}>Anulowana</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="price" id="price" step="0.01" min="0" value="{{ $subscription->price }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">PLN</span>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3" id="next_payment_date_container" {{ $subscription->subscription_type == 'automatic' ? '' : 'hidden' }}>
                                <label for="next_payment_date" class="block text-sm font-medium text-gray-700">Data następnej płatności</label>
                                <input type="date" name="next_payment_date" id="next_payment_date" value="{{ $subscription->next_payment_date ? $subscription->next_payment_date->format('Y-m-d') : '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Tylko dla automatycznych subskrypcji</p>
                                @error('next_payment_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="trial_ends_at" class="block text-sm font-medium text-gray-700">Data zakończenia okresu próbnego</label>
                                <input type="date" name="trial_ends_at" id="trial_ends_at" value="{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d') : '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Pozostaw puste jeśli nie dotyczy</p>
                                @error('trial_ends_at')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="card" {{ $subscription->payment_method == 'card' ? 'selected' : '' }}>Karta płatnicza</option>
                                    <option value="paypal" {{ $subscription->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bank_transfer" {{ $subscription->payment_method == 'bank_transfer' ? 'selected' : '' }}>Przelew bankowy</option>
                                    <option value="cash" {{ $subscription->payment_method == 'cash' ? 'selected' : '' }}>Gotówka</option>
                                    <option value="free" {{ $subscription->payment_method == 'free' ? 'selected' : '' }}>Bezpłatnie</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="last_payment_id" class="block text-sm font-medium text-gray-700">ID ostatniej płatności</label>
                                <input type="text" name="last_payment_id" id="last_payment_id" value="{{ $subscription->last_payment_id }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">np. ID transakcji, nr faktury</p>
                                @error('last_payment_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        @if(isset($payments) && count($payments) > 0)
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Historia płatności</h3>
                            <div class="mt-4 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Data</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kwota</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Metoda płatności</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nr transakcji</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $payment->created_at->format('d.m.Y') }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ number_format($payment->amount, 2) }} PLN</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $payment->status == 'paid' ? 'green' : 'red' }}-100 text-{{ $payment->status == 'paid' ? 'green' : 'red' }}-800">
                                                    {{ $payment->status == 'paid' ? 'Opłacona' : 'Nieopłacona' }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $payment->payment_method }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $payment->transaction_id }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex justify-between">
                                <div>
                                    @if($subscription->status != 'cancelled')
                                    <a href="{{ route('admin.subscriptions.cancel', $subscription->id) }}" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                       onclick="return confirm('Czy na pewno chcesz anulować tę subskrypcję?')">
                                       Anuluj subskrypcję
                                    </a>
                                    @else
                                    <span class="text-gray-500 text-sm">Subskrypcja jest już anulowana</span>
                                    @endif
                                </div>
                                <div class="flex">
                                    <a href="{{ route('admin.subscriptions.users') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                        Anuluj
                                    </a>
                                    <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Zapisz zmiany
                                    </button>
                                </div>
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
            const nextPaymentDateContainer = document.getElementById('next_payment_date_container');
            const planSelect = document.getElementById('plan_id');
            const priceInput = document.getElementById('price');
            
            // Pokaż/ukryj opcje odnowienia w zależności od typu subskrypcji
            subscriptionTypeSelect.addEventListener('change', function() {
                if (this.value === 'automatic') {
                    renewalOptionsDiv.classList.remove('hidden');
                    nextPaymentDateContainer.classList.remove('hidden');
                } else {
                    renewalOptionsDiv.classList.add('hidden');
                    nextPaymentDateContainer.classList.add('hidden');
                }
            });
            
            // Ustawienie ceny na podstawie wybranego planu
            planSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value !== '' && !priceInput.dataset.manuallyChanged) {
                    priceInput.value = selectedOption.dataset.price;
                }
            });
            
            // Oznacz, że cena została ręcznie zmieniona
            priceInput.addEventListener('input', function() {
                this.dataset.manuallyChanged = true;
            });
        });
    </script>
    @endpush
</x-admin-layout> 