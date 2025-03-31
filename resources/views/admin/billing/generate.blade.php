@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Generowanie faktur</h1>
            
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                            <i class="fas fa-home mr-2"></i>
                            Panel
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                            <a href="{{ route('admin.billing.invoices') }}" class="text-gray-700 hover:text-blue-600">
                                Faktury
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                            <span class="text-gray-500">Generowanie faktur</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Komunikaty -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Karty informacyjne -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Automatyczne generowanie -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $settings->auto_generate ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                            <i class="fas {{ $settings->auto_generate ? 'fa-check-circle' : 'fa-times-circle' }} text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-medium text-gray-900">Automatyczne generowanie</h2>
                            <p class="text-sm text-gray-500">{{ $settings->auto_generate ? 'Włączone' : 'Wyłączone' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dzień generowania -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-medium text-gray-900">Dzień generowania</h2>
                            <p class="text-sm text-gray-500">{{ $settings->generation_day }} dzień miesiąca</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Oczekujące faktury -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $pendingCount > 0 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600' }}">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-medium text-gray-900">Oczekujące faktury</h2>
                            <p class="text-sm text-gray-500">{{ $pendingCount }} subskrypcji wymaga fakturowania</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ostatnia faktura -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-file-invoice text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-medium text-gray-900">Ostatnia faktura</h2>
                            @if($lastInvoice)
                                <p class="text-sm text-gray-500">{{ $lastInvoice->number }} ({{ $lastInvoice->created_at->format('d.m.Y') }})</p>
                            @else
                                <p class="text-sm text-gray-500">Brak automatycznych faktur</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel akcji -->
        <div class="bg-white shadow-md rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Ręczne generowanie faktur</h2>
                <p class="text-gray-500 mb-6">
                    Ręczne generowanie faktur spowoduje utworzenie dokumentów dla wszystkich aktywnych subskrypcji, 
                    które wymagają fakturowania. System utworzy faktury zgodnie z ustawieniami zdefiniowanymi w 
                    sekcji ustawień faktur.
                </p>
                
                <form action="{{ route('admin.billing.generate.run') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md flex items-center justify-center">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Generuj faktury dla wszystkich oczekujących subskrypcji
                        </button>
                        
                        <a href="{{ route('admin.billing.settings') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-md flex items-center justify-center">
                            <i class="fas fa-cog mr-2"></i>
                            Zmień ustawienia
                        </a>
                    </div>
                </form>
                
                @if($settings->auto_generate)
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        Automatyczne generowanie faktur jest włączone. Faktury są generowane {{ $settings->generation_day }} dnia każdego miesiąca.
                    </div>
                @else
                    <div class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-exclamation-triangle mr-1 text-yellow-500"></i>
                        Automatyczne generowanie faktur jest wyłączone. Faktury muszą być generowane ręcznie.
                    </div>
                @endif
            </div>
        </div>

        <!-- Lista subskrypcji oczekujących na fakturę -->
        <div class="bg-white shadow-md rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Subskrypcje oczekujące na fakturowanie</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Lista aktywnych subskrypcji, dla których można wygenerować faktury
                </p>
            </div>
            
            @if($activeSubscriptions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Użytkownik
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data następnego rozliczenia
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($activeSubscriptions as $subscription)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $subscription->user_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $subscription->user_email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $subscription->plan_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(\Carbon\Carbon::parse($subscription->next_billing_date)->isPast())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ \Carbon\Carbon::parse($subscription->next_billing_date)->format('d.m.Y') }} (zaległa)
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($subscription->next_billing_date)->format('d.m.Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(\Carbon\Carbon::parse($subscription->next_billing_date)->isPast())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Oczekująca
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktywna
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center">
                    <p class="text-gray-500">Brak aktywnych subskrypcji wymagających fakturowania</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 