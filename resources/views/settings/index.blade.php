<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ustawienia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dane firmy -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Dane firmy</h3>
                                
                                <div>
                                    <x-input-label for="company_name" value="Nazwa firmy" />
                                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $companyProfile->company_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                                </div>

                                <div>
                                    <x-input-label for="legal_form" value="Forma prawna" />
                                    <x-text-input id="legal_form" name="legal_form" type="text" class="mt-1 block w-full" :value="old('legal_form', $companyProfile->legal_form)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('legal_form')" />
                                </div>

                                <div>
                                    <x-input-label for="tax_number" value="NIP" />
                                    <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full" :value="old('tax_number', $companyProfile->tax_number)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
                                </div>

                                <div>
                                    <x-input-label for="regon" value="REGON" />
                                    <x-text-input id="regon" name="regon" type="text" class="mt-1 block w-full" :value="old('regon', $companyProfile->regon)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('regon')" />
                                </div>
                            </div>

                            <!-- Adres -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Adres</h3>
                                
                                <div>
                                    <x-input-label for="street" value="Ulica" />
                                    <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $companyProfile->street)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('street')" />
                                </div>

                                <div>
                                    <x-input-label for="postal_code" value="Kod pocztowy" />
                                    <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $companyProfile->postal_code)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                                </div>

                                <div>
                                    <x-input-label for="city" value="Miasto" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $companyProfile->city)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <div>
                                    <x-input-label for="country" value="Kraj" />
                                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $companyProfile->country)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                </div>
                            </div>

                            <!-- Kontakt -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Kontakt</h3>
                                
                                <div>
                                    <x-input-label for="phone" value="Telefon" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $companyProfile->phone)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <div>
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $companyProfile->email)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <div>
                                    <x-input-label for="website" value="Strona internetowa" />
                                    <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $companyProfile->website)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('website')" />
                                </div>
                            </div>

                            <!-- Ustawienia faktur -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Ustawienia faktur</h3>
                                
                                <div>
                                    <x-input-label for="invoice_prefix" value="Prefiks numeru faktury" />
                                    <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full" :value="old('invoice_prefix', $companyProfile->invoice_prefix)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice_prefix')" />
                                </div>

                                <div>
                                    <x-input-label for="invoice_numbering_pattern" value="Wzorzec numeracji" />
                                    <x-text-input id="invoice_numbering_pattern" name="invoice_numbering_pattern" type="text" class="mt-1 block w-full" :value="old('invoice_numbering_pattern', $companyProfile->invoice_numbering_pattern)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice_numbering_pattern')" />
                                </div>

                                <div>
                                    <x-input-label for="invoice_next_number" value="Następny numer" />
                                    <x-text-input id="invoice_next_number" name="invoice_next_number" type="number" class="mt-1 block w-full" :value="old('invoice_next_number', $companyProfile->invoice_next_number)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice_next_number')" />
                                </div>

                                <div>
                                    <x-input-label for="invoice_payment_days" value="Domyślny termin płatności (dni)" />
                                    <x-text-input id="invoice_payment_days" name="invoice_payment_days" type="number" class="mt-1 block w-full" :value="old('invoice_payment_days', $companyProfile->invoice_payment_days)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice_payment_days')" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-primary-button>
                                {{ __('Zapisz ustawienia') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 