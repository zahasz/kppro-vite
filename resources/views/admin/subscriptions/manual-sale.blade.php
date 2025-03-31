@extends('layouts.admin')

@section('title', 'Ręczna sprzedaż subskrypcji')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ręczna sprzedaż subskrypcji</h1>
        <a href="{{ route('admin.subscriptions.users') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
            Powrót do listy subskrypcji
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Informacje o ręcznej sprzedaży</h2>
            <p class="text-gray-600">
                Ten formularz pozwala na ręczną sprzedaż subskrypcji dla klienta, który zapłacił gotówką, przelewem lub kartą przez terminal.
                System automatycznie utworzy subskrypcję, płatność i fakturę.
            </p>
        </div>

        <form action="{{ route('admin.subscriptions.manual-sale') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Wybór użytkownika -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Użytkownik</label>
                    <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Wybierz użytkownika</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wybór planu subskrypcji -->
                <div>
                    <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700 mb-1">Plan subskrypcji</label>
                    <select id="subscription_plan_id" name="subscription_plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Wybierz plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ number_format($plan->price, 2) }} {{ $plan->currency }})
                            </option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Metoda płatności -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metoda płatności</label>
                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($paymentMethods as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Szczegóły płatności -->
                <div>
                    <label for="payment_details" class="block text-sm font-medium text-gray-700 mb-1">Szczegóły płatności</label>
                    <input type="text" id="payment_details" name="payment_details" value="{{ old('payment_details') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Np. numer potwierdzenia, ostatnie 4 cyfry karty itp.">
                    @error('payment_details')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notatki administratora -->
            <div class="mb-6">
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">Notatki administratora</label>
                <textarea id="admin_notes" name="admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Opcjonalne notatki dotyczące tej sprzedaży">{{ old('admin_notes') }}</textarea>
                @error('admin_notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gray-50 p-4 rounded-md mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Co się stanie po zapisaniu?</h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>Zostanie utworzona nowa aktywna subskrypcja dla wybranego użytkownika</li>
                    <li>Automatycznie zostanie utworzony rekord płatności</li>
                    <li>System wygeneruje fakturę dla tej płatności</li>
                    <li>Użytkownik otrzyma powiadomienie o aktywacji subskrypcji</li>
                </ul>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sprzedaj subskrypcję
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 