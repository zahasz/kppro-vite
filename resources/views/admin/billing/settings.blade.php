@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Ustawienia faktur i płatności</h1>
            
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

        <!-- Zakładki -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" id="settingsTabs" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block py-2 px-4 text-sm font-medium text-center text-gray-500 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 active" id="billing-tab" data-tabs-target="#billing-content" type="button" role="tab" aria-controls="billing-content" aria-selected="true">
                        Fakturowanie
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block py-2 px-4 text-sm font-medium text-center text-gray-500 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="payment-tab" data-tabs-target="#payment-content" type="button" role="tab" aria-controls="payment-content" aria-selected="false">
                        Płatności
                    </button>
                </li>
            </ul>
        </div>

        <!-- Treść zakładek -->
        <div id="settingsTabsContent">
            <!-- Zakładka ustawień faktur -->
            <div class="block" id="billing-content" role="tabpanel" aria-labelledby="billing-tab">
                <!-- Formularz ustawień faktur -->
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
                            Zapisz ustawienia faktur
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zakładka ustawień płatności -->
            <div class="hidden" id="payment-content" role="tabpanel" aria-labelledby="payment-tab">
                <form action="{{ route('admin.payment.settings.update') }}" method="POST" class="bg-white rounded-lg shadow-md overflow-hidden">
                    @csrf
                    @method('PUT')

                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Ogólne ustawienia płatności</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bramki płatności -->
                            <div class="md:col-span-2">
                                <h3 class="text-md font-medium text-gray-700 mb-2">Aktywne bramki płatności</h3>
                                <p class="text-sm text-gray-500 mb-4">Zarządzaj bramkami płatności w sekcji <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:underline">Bramki płatności</a>.</p>
                            </div>

                            <!-- Automatyczne próby płatności -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="auto_retry_failed_payments" name="auto_retry_failed_payments" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $paymentSettings->auto_retry_failed_payments ?? false ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="auto_retry_failed_payments" class="font-medium text-gray-700">Automatyczne ponowne próby płatności</label>
                                    <p class="text-gray-500">Ponowna próba pobrania płatności, gdy pierwotna próba się nie powiedzie</p>
                                </div>
                            </div>

                            <!-- Liczba prób -->
                            <div>
                                <label for="payment_retry_attempts" class="block text-sm font-medium text-gray-700">Liczba ponownych prób płatności</label>
                                <div class="mt-1">
                                    <input type="number" name="payment_retry_attempts" id="payment_retry_attempts" min="1" max="5" value="{{ $paymentSettings->payment_retry_attempts ?? 3 }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Ile razy system powinien próbować ponownie pobrać płatność</p>
                            </div>

                            <!-- Odstęp między próbami -->
                            <div>
                                <label for="payment_retry_interval" class="block text-sm font-medium text-gray-700">Odstęp między próbami (dni)</label>
                                <div class="mt-1">
                                    <input type="number" name="payment_retry_interval" id="payment_retry_interval" min="1" max="10" value="{{ $paymentSettings->payment_retry_interval ?? 3 }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Liczba dni między kolejnymi próbami płatności</p>
                            </div>

                            <!-- Okres karencji -->
                            <div>
                                <label for="grace_period_days" class="block text-sm font-medium text-gray-700">Okres karencji (dni)</label>
                                <div class="mt-1">
                                    <input type="number" name="grace_period_days" id="grace_period_days" min="0" max="30" value="{{ $paymentSettings->grace_period_days ?? 3 }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Liczba dni, przez które subskrypcja pozostaje aktywna po nieudanej płatności</p>
                            </div>

                            <!-- Domyślna bramka płatności -->
                            <div>
                                <label for="default_payment_gateway" class="block text-sm font-medium text-gray-700">Domyślna bramka płatności</label>
                                <div class="mt-1">
                                    <select name="default_payment_gateway" id="default_payment_gateway" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">-- Wybierz bramkę --</option>
                                        @foreach($paymentGateways as $gateway)
                                            <option value="{{ $gateway->code }}" {{ ($paymentSettings->default_payment_gateway ?? '') == $gateway->code ? 'selected' : '' }}>{{ $gateway->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Bramka domyślnie wybrana podczas płatności</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Ustawienia odnowień subskrypcji</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Powiadomienia o zbliżającym się terminie -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="renewal_notifications" name="renewal_notifications" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $paymentSettings->renewal_notifications ?? true ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="renewal_notifications" class="font-medium text-gray-700">Powiadomienia o odnowieniu</label>
                                    <p class="text-gray-500">Wysyłaj powiadomienia e-mail o zbliżającym się odnowieniu subskrypcji</p>
                                </div>
                            </div>

                            <!-- Liczba dni przed odnowieniem -->
                            <div>
                                <label for="renewal_notification_days" class="block text-sm font-medium text-gray-700">Dni przed odnowieniem</label>
                                <div class="mt-1">
                                    <input type="number" name="renewal_notification_days" id="renewal_notification_days" min="1" max="30" value="{{ $paymentSettings->renewal_notification_days ?? 7 }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Na ile dni przed odnowieniem wysłać powiadomienie</p>
                            </div>

                            <!-- Automatyczne anulowanie -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="auto_cancel_after_failed_payments" name="auto_cancel_after_failed_payments" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $paymentSettings->auto_cancel_after_failed_payments ?? true ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="auto_cancel_after_failed_payments" class="font-medium text-gray-700">Automatyczne anulowanie po niepowodzeniu</label>
                                    <p class="text-gray-500">Automatycznie anuluj subskrypcję po wyczerpaniu wszystkich prób płatności</p>
                                </div>
                            </div>

                            <!-- Podejmij próbę przed wygaśnięciem -->
                            <div>
                                <label for="renewal_charge_days_before" class="block text-sm font-medium text-gray-700">Pobierz opłatę (dni przed wygaśnięciem)</label>
                                <div class="mt-1">
                                    <input type="number" name="renewal_charge_days_before" id="renewal_charge_days_before" min="0" max="7" value="{{ $paymentSettings->renewal_charge_days_before ?? 3 }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Na ile dni przed wygaśnięciem próbować pobrać opłatę za odnowienie</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Integracja z systemami zewnętrznymi</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Księgowość online -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="enable_accounting_integration" name="enable_accounting_integration" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $paymentSettings->enable_accounting_integration ?? false ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_accounting_integration" class="font-medium text-gray-700">Integracja z systemem księgowym</label>
                                    <p class="text-gray-500">Włącz integrację z zewnętrznym systemem księgowym</p>
                                </div>
                            </div>

                            <!-- URL API systemu księgowego -->
                            <div>
                                <label for="accounting_api_url" class="block text-sm font-medium text-gray-700">URL API systemu księgowego</label>
                                <div class="mt-1">
                                    <input type="url" name="accounting_api_url" id="accounting_api_url" value="{{ $paymentSettings->accounting_api_url ?? '' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">URL endpointu API systemu księgowego</p>
                            </div>

                            <!-- Klucz API systemu księgowego -->
                            <div>
                                <label for="accounting_api_key" class="block text-sm font-medium text-gray-700">Klucz API systemu księgowego</label>
                                <div class="mt-1">
                                    <input type="password" name="accounting_api_key" id="accounting_api_key" value="{{ $paymentSettings->accounting_api_key ?? '' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Klucz API do autoryzacji w systemie księgowym</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Zapisz ustawienia płatności
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('[role="tabpanel"]');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Ukryj wszystkie panele
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Usuń aktywne klasy z zakładek
                tabs.forEach(t => {
                    t.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                    t.setAttribute('aria-selected', 'false');
                });
                
                // Pokaż aktywny panel
                const target = document.querySelector(tab.dataset.tabsTarget);
                target.classList.remove('hidden');
                target.classList.add('block');
                
                // Aktywuj zakładkę
                tab.classList.remove('border-transparent', 'text-gray-500');
                tab.classList.add('active', 'border-blue-600', 'text-blue-600');
                tab.setAttribute('aria-selected', 'true');
            });
        });
    });
</script>
@endpush
@endsection 