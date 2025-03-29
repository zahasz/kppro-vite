<x-admin-layout>
    <x-slot name="header">
        Zarządzanie planami subskrypcji
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Plany subskrypcji</h2>
                <p class="mt-1 text-sm text-gray-600">Zarządzaj dostępnymi planami subskrypcji w systemie.</p>
            </div>
            <div>
                <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-800 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Dodaj nowy plan
                </a>
            </div>
        </div>

        <!-- Karty planów subskrypcji -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-5 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                            <div class="flex items-center space-x-2">
                                @if(isset($plan->is_active) && $plan->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktywny</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Nieaktywny</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-3xl font-bold text-gray-900">{{ number_format($plan->price ?? 0, 2) }}</span>
                            <span class="text-gray-500 ml-1">zł</span>
                            <span class="text-gray-500 ml-1">/ 
                                @if(isset($plan->interval) && $plan->interval == 'monthly')
                                    miesiąc
                                @elseif(isset($plan->interval) && $plan->interval == 'quarterly')
                                    kwartał
                                @elseif(isset($plan->interval) && $plan->interval == 'yearly')
                                    rok
                                @else
                                    {{ $plan->interval ?? 'miesiąc' }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="text-sm text-gray-600 mb-4">
                            {{ $plan->description ?? 'Brak opisu' }}
                        </div>

                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900">Funkcje:</h4>
                            <ul class="space-y-2">
                                @if(isset($plan->features) && is_array($plan->features) && count($plan->features) > 0)
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-start">
                                            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="text-gray-500">Brak zdefiniowanych funkcji</li>
                                @endif
                            </ul>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Typ subskrypcji:</span>
                                <span class="font-medium">
                                    @if(isset($plan->subscription_type) && $plan->subscription_type == 'manual')
                                        Ręczna
                                    @elseif(isset($plan->subscription_type) && $plan->subscription_type == 'automatic')
                                        Automatyczna
                                    @elseif(isset($plan->subscription_type) && $plan->subscription_type == 'both')
                                        Ręczna i automatyczna
                                    @else
                                        Nieokreślony
                                    @endif
                                </span>
                            </div>
                            
                            @if(isset($plan->trial_period_days) && $plan->trial_period_days > 0)
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-gray-600">Okres próbny:</span>
                                    <span class="font-medium">{{ $plan->trial_period_days }} dni</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-gray-600">Aktywnych subskrypcji:</span>
                                <span class="font-medium">{{ $plan->activeSubscriptions()->count() }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-2">
                            <a href="{{ route('admin.subscriptions.edit', $plan->id) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edytuj
                            </a>
                            <form action="{{ route('admin.subscriptions.destroy', $plan->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć ten plan? Spowoduje to również usunięcie wszystkich powiązanych subskrypcji.')" class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Usuń
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pusta lista -->
        @if($plans->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Brak planów subskrypcji</h3>
                <p class="mt-1 text-sm text-gray-500">Nie utworzono jeszcze żadnych planów subskrypcji.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-800 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Utwórz pierwszy plan
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout> 