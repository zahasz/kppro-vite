@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Ustawienia faktur</h1>
            
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
                            <span class="text-gray-500">Ustawienia</span>
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

        <!-- Formularz ustawień -->
        <form action="{{ route('admin.billing.settings.update') }}" method="POST" class="bg-white rounded-lg shadow-md overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Podstawowe ustawienia faktur</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Automatyczne generowanie faktur -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="auto_generate" name="auto_generate" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $settings->auto_generate ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="auto_generate" class="font-medium text-gray-700">Automatyczne generowanie faktur</label>
                            <p class="text-gray-500">System będzie automatycznie generował faktury dla aktywnych subskrypcji</p>
                        </div>
                    </div>

                    <!-- Dzień generowania -->
                    <div>
                        <label for="generation_day" class="block text-sm font-medium text-gray-700">Dzień generowania faktur</label>
                        <div class="mt-1">
                            <input type="number" name="generation_day" id="generation_day" min="1" max="28" value="{{ $settings->generation_day }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Dzień miesiąca, w którym będą generowane faktury (1-28)</p>
                    </div>

                    <!-- Prefix faktury -->
                    <div>
                        <label for="invoice_prefix" class="block text-sm font-medium text-gray-700">Prefix faktury</label>
                        <div class="mt-1">
                            <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ $settings->invoice_prefix }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Prefix dodawany przed numerem faktury (np. FV/)</p>
                    </div>

                    <!-- Suffix faktury -->
                    <div>
                        <label for="invoice_suffix" class="block text-sm font-medium text-gray-700">Suffix faktury</label>
                        <div class="mt-1">
                            <input type="text" name="invoice_suffix" id="invoice_suffix" value="{{ $settings->invoice_suffix }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Suffix dodawany po numerze faktury (np. /SUB)</p>
                    </div>

                    <!-- Reset numeracji -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="reset_numbering" name="reset_numbering" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $settings->reset_numbering ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="reset_numbering" class="font-medium text-gray-700">Reset numeracji co miesiąc</label>
                            <p class="text-gray-500">Numeracja faktur będzie resetowana co miesiąc</p>
                        </div>
                    </div>

                    <!-- Termin płatności -->
                    <div>
                        <label for="payment_days" class="block text-sm font-medium text-gray-700">Termin płatności (dni)</label>
                        <div class="mt-1">
                            <input type="number" name="payment_days" id="payment_days" min="0" max="60" value="{{ $settings->payment_days }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Domyślny termin płatności w dniach</p>
                    </div>

                    <!-- Domyślna waluta -->
                    <div>
                        <label for="default_currency" class="block text-sm font-medium text-gray-700">Domyślna waluta</label>
                        <div class="mt-1">
                            <select name="default_currency" id="default_currency" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="PLN" {{ $settings->default_currency == 'PLN' ? 'selected' : '' }}>PLN - Polski złoty</option>
                                <option value="EUR" {{ $settings->default_currency == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="USD" {{ $settings->default_currency == 'USD' ? 'selected' : '' }}>USD - Dolar amerykański</option>
                                <option value="GBP" {{ $settings->default_currency == 'GBP' ? 'selected' : '' }}>GBP - Funt brytyjski</option>
                                <option value="CHF" {{ $settings->default_currency == 'CHF' ? 'selected' : '' }}>CHF - Frank szwajcarski</option>
                            </select>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Domyślna waluta dla wystawianych faktur</p>
                    </div>

                    <!-- Domyślna stawka VAT -->
                    <div>
                        <label for="default_tax_rate" class="block text-sm font-medium text-gray-700">Domyślna stawka VAT (%)</label>
                        <div class="mt-1">
                            <input type="number" name="default_tax_rate" id="default_tax_rate" min="0" max="100" step="0.01" value="{{ $settings->default_tax_rate }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Domyślna stawka podatku VAT dla faktur subskrypcyjnych</p>
                    </div>

                    <!-- NIP firmy -->
                    <div>
                        <label for="vat_number" class="block text-sm font-medium text-gray-700">NIP firmy</label>
                        <div class="mt-1">
                            <input type="text" name="vat_number" id="vat_number" value="{{ $settings->vat_number }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">NIP wystawiającego faktury</p>
                    </div>

                    <!-- E-mail powiadomienia -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="email_notifications" name="email_notifications" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $settings->email_notifications ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="email_notifications" class="font-medium text-gray-700">Powiadomienia e-mail</label>
                            <p class="text-gray-500">Wysyłaj powiadomienia e-mail o nowych fakturach</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Uwagi na fakturach</h2>
                
                <div class="mb-4">
                    <label for="invoice_notes" class="block text-sm font-medium text-gray-700">Standardowe uwagi na fakturach</label>
                    <div class="mt-1">
                        <textarea id="invoice_notes" name="invoice_notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings->invoice_notes }}</textarea>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Te uwagi będą automatycznie dodawane do każdej faktury</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Zapisz ustawienia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 