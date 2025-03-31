@extends('layouts.admin')

@section('title', 'Plany subskrypcyjne')

@section('content')
<div class="container px-6 mx-auto">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Plany subskrypcyjne</h2>
                <p class="mt-1 text-sm text-gray-600">Zarządzaj dostępnymi planami subskrypcji w aplikacji.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:bg-steel-blue-700 active:bg-steel-blue-800 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Dodaj nowy plan
                </a>
                <a href="{{ route('admin.subscriptions.permissions') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                    </svg>
                    Zarządzaj uprawnieniami
                </a>
            </div>
        </div>

        @include('admin.partials.success-alert')
        @include('admin.partials.error-alert')
        
        <!-- Pricing Cards -->
        <div class="grid gap-6 mb-8 md:grid-cols-3">
            @foreach($plans as $plan)
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm border-t-4 {{ $plan->code === 'basic' ? 'border-blue-500' : ($plan->code === 'business' ? 'border-purple-500' : 'border-green-500') }}">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-600">
                        {{ $plan->name }}
                    </h4>
                    <span class="px-2 py-1 text-xs font-medium leading-tight text-white bg-gray-600 rounded-full">
                        {{ $plan->billing_period }}
                    </span>
                </div>
                <div class="flex items-baseline mb-4">
                    <span class="text-3xl font-semibold text-gray-700">
                        {{ number_format($plan->price, 2) }} zł
                    </span>
                    <span class="ml-1 text-sm text-gray-500">/ {{ $plan->billing_period === 'monthly' ? 'miesiąc' : 'rok' }}</span>
                </div>
                <p class="mb-4 text-sm text-gray-600">
                    {{ $plan->description }}
                </p>
                
                <!-- Limity -->
                <div class="mb-4">
                    <h5 class="mb-2 text-sm font-semibold text-gray-600">Limity:</h5>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Faktury: {{ $plan->max_invoices }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Produkty: {{ $plan->max_products }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Kontrahenci: {{ $plan->max_clients }}
                        </li>
                    </ul>
                </div>

                <!-- Funkcje dostępne w planie -->
                <div class="mb-4">
                    <h5 class="mb-2 text-sm font-semibold text-gray-600">Główne funkcje:</h5>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @foreach($plan->permissions as $permission)
                            @if($permission->category !== 'limits' && $loop->index < 5)
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $permission->name }}
                            </li>
                            @endif
                        @endforeach
                        
                        @if($plan->permissions->count() > 5)
                            <li class="text-sm text-gray-500 mt-2">
                                +{{ $plan->permissions->count() - 5 }} więcej funkcji
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="flex justify-between mt-4">
                    <a href="{{ route('admin.subscriptions.edit', $plan->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-steel-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 transition ease-in-out duration-150">
                        Edytuj
                    </a>
                    <form action="{{ route('admin.subscriptions.destroy', $plan->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć ten plan?')" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                            Usuń
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Subskrypcje użytkowników</h2>
                <p class="mt-1 text-sm text-gray-600">Najnowsze aktywne subskrypcje użytkowników w systemie.</p>
            </div>
            <div>
                <a href="{{ route('admin.subscriptions.users') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 transition ease-in-out duration-150">
                    Zobacz wszystkie subskrypcje
                </a>
            </div>
        </div>

        <!-- Tabela subskrypcji -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownik</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metoda płatności</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data rozpoczęcia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data zakończenia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptions as $subscription)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($subscription->user->name) }}&background=random" alt="{{ $subscription->user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $subscription->plan->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->status == 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktywna
                                    </span>
                                @elseif($subscription->status == 'trial')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Trial
                                    </span>
                                @elseif($subscription->status == 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Oczekująca
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subscription->payment_method ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subscription->start_date ? $subscription->start_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subscription->end_date ? $subscription->end_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.subscriptions.edit-user-subscription', $subscription->id) }}" class="text-steel-blue-600 hover:text-steel-blue-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 