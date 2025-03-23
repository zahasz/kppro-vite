@extends('layouts.app')

@section('title', 'Edycja profilu')

@section('content')
<div class="space-y-6">
    <!-- Menu nawigacyjne profilu -->
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <nav class="flex flex-wrap gap-2">
            <a href="#dane-uzytkownika" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Dane użytkownika</a>
            <a href="#dane-firmy" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Dane firmy</a>
            <a href="#dane-bankowe" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Dane bankowe</a>
            <a href="#ustawienia-faktur" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Ustawienia faktur</a>
            <a href="#subskrypcja" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Subskrypcja</a>
            <a href="#bezpieczenstwo" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">Bezpieczeństwo</a>
        </nav>
    </div>

    <!-- Sekcja 0: Informacje o subskrypcji -->
    <div id="subskrypcja" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Twoja subskrypcja
            </h2>
        </div>
        <div class="max-w-xl">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-blue-900">Plan: <span class="font-bold">Premium</span></h3>
                        <p class="text-blue-700">Aktywna do: 15.12.2023</p>
                    </div>
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Aktywna
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Korzyści Twojego planu:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-gray-600">
                        <li>Nieograniczona liczba faktur</li>
                        <li>Dostęp do wszystkich modułów</li>
                        <li>Priorytetowe wsparcie techniczne</li>
                        <li>Eksport danych w wielu formatach</li>
                        <li>Automatyczne kopie zapasowe</li>
                    </ul>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Zmień plan
                    </a>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Historia płatności
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sekcja 1: Dane użytkownika -->
    <div id="dane-uzytkownika" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Dane użytkownika
            </h2>
        </div>
        <div class="max-w-xl">
            @if (session('status') === 'profile-updated')
                <div class="mb-4 font-medium text-sm text-green-600">
                    Profil został zaktualizowany pomyślnie.
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img class="h-16 w-16 object-cover rounded-full" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Zdjęcie profilowe
                        </label>
                        <input type="file" name="avatar" class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                    </div>
                </div>

                <div>
                    <x-input-label for="username" value="Nazwa użytkownika" />
                    <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" autofocus autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="first_name" value="Imię" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" autocomplete="given-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                    </div>

                    <div>
                        <x-input-label for="last_name" value="Nazwisko" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" autocomplete="family-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" autocomplete="email" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Telefon" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="position" value="Stanowisko" />
                    <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" />
                    <x-input-error class="mt-2" :messages="$errors->get('position')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Zapisz dane użytkownika') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sekcja 2: Dane firmowe -->
    <div id="dane-firmy" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Dane firmy
            </h2>
        </div>
        <div>
            @include('profile.partials.company-profile-form')
        </div>
    </div>

    <!-- Sekcja 3: Dane bankowe -->
    <div id="dane-bankowe" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Dane bankowe
            </h2>
        </div>
        <div class="max-w-xl">
            <p class="mt-1 text-sm text-gray-600 mb-4">
                Zarządzaj swoimi kontami bankowymi w oddzielnej sekcji. Konta bankowe są używane w fakturach i innych dokumentach finansowych.
            </p>
            
            @if($user->companyProfile)
                <div class="mb-6">
                    <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Przejdź do zarządzania kontami bankowymi') }}
                    </a>
                </div>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <p>{{ __('Musisz najpierw utworzyć profil firmy, aby móc zarządzać kontami bankowymi.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sekcja 4: Ustawienia faktur -->
    <div id="ustawienia-faktur" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Ustawienia faktur
            </h2>
        </div>
        <div class="max-w-xl">
            @if(!$user->companyProfile)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <p>{{ __('Musisz najpierw utworzyć profil firmy, aby móc zarządzać ustawieniami faktur.') }}</p>
                </div>
            @else
                <form method="post" action="{{ route('company-profile.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Prefiks numeru faktury -->
                        <div>
                            <x-input-label for="invoice_prefix" :value="__('Prefiks numeru faktury')" />
                            <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full" :value="old('invoice_prefix', $user->companyProfile?->invoice_prefix)" />
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_prefix')" />
                        </div>

                        <!-- Wzorzec numeracji faktur -->
                        <div>
                            <x-input-label for="invoice_numbering_pattern" :value="__('Wzorzec numeracji faktur')" />
                            <x-text-input id="invoice_numbering_pattern" name="invoice_numbering_pattern" type="text" class="mt-1 block w-full" :value="old('invoice_numbering_pattern', $user->companyProfile?->invoice_numbering_pattern)" />
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_numbering_pattern')" />
                            <p class="mt-1 text-sm text-gray-500">Dostępne zmienne: {YEAR}, {MONTH}, {DAY}, {NUMBER}</p>
                        </div>

                        <!-- Następny numer faktury -->
                        <div>
                            <x-input-label for="invoice_next_number" :value="__('Następny numer faktury')" />
                            <x-text-input id="invoice_next_number" name="invoice_next_number" type="number" min="1" class="mt-1 block w-full" :value="old('invoice_next_number', $user->companyProfile?->invoice_next_number)" />
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_next_number')" />
                        </div>

                        <!-- Domyślny termin płatności -->
                        <div>
                            <x-input-label for="invoice_payment_days" :value="__('Domyślny termin płatności (dni)')" />
                            <x-text-input id="invoice_payment_days" name="invoice_payment_days" type="number" min="0" class="mt-1 block w-full" :value="old('invoice_payment_days', $user->companyProfile?->invoice_payment_days)" />
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_payment_days')" />
                        </div>

                        <!-- Domyślna metoda płatności -->
                        <div>
                            <x-input-label for="default_payment_method" :value="__('Domyślna metoda płatności')" />
                            <select id="default_payment_method" name="default_payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="przelew" {{ old('default_payment_method', $user->companyProfile?->default_payment_method) === 'przelew' ? 'selected' : '' }}>Przelew</option>
                                <option value="gotówka" {{ old('default_payment_method', $user->companyProfile?->default_payment_method) === 'gotówka' ? 'selected' : '' }}>Gotówka</option>
                                <option value="karta" {{ old('default_payment_method', $user->companyProfile?->default_payment_method) === 'karta' ? 'selected' : '' }}>Karta płatnicza</option>
                                <option value="blik" {{ old('default_payment_method', $user->companyProfile?->default_payment_method) === 'blik' ? 'selected' : '' }}>BLIK</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_payment_method')" />
                        </div>

                        <!-- Domyślna waluta -->
                        <div>
                            <x-input-label for="default_currency" :value="__('Domyślna waluta')" />
                            <select id="default_currency" name="default_currency" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="PLN" {{ old('default_currency', $user->companyProfile?->default_currency) === 'PLN' ? 'selected' : '' }}>PLN</option>
                                <option value="EUR" {{ old('default_currency', $user->companyProfile?->default_currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="USD" {{ old('default_currency', $user->companyProfile?->default_currency) === 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="GBP" {{ old('default_currency', $user->companyProfile?->default_currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_currency')" />
                        </div>

                        <!-- Domyślne uwagi na fakturze -->
                        <div class="md:col-span-2">
                            <x-input-label for="invoice_notes" :value="__('Domyślne uwagi na fakturze')" />
                            <textarea id="invoice_notes" name="invoice_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2">{{ old('invoice_notes', $user->companyProfile?->invoice_notes) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_notes')" />
                        </div>

                        <!-- Stopka faktury -->
                        <div class="md:col-span-2">
                            <x-input-label for="invoice_footer" :value="__('Stopka faktury')" />
                            <textarea id="invoice_footer" name="invoice_footer" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2">{{ $user->companyProfile ? $user->companyProfile->invoice_footer : '' }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('invoice_footer')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Zapisz ustawienia faktur') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Sekcja 5: Bezpieczeństwo -->
    <div id="bezpieczenstwo" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium text-gray-900">
                Bezpieczeństwo
            </h2>
        </div>
        <div class="max-w-xl space-y-6">
            <!-- Zmiana hasła -->
            <div>
                <h3 class="text-lg font-medium text-gray-900">Zmiana hasła</h3>
                <form method="post" action="{{ route('password.update') }}" class="mt-4">
                    @csrf
                    @method('put')

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="current_password" :value="__('Aktualne hasło')" />
                            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Nowe hasło')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Potwierdź nowe hasło')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-primary-button>{{ __('Zmień hasło') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Dwuskładnikowe uwierzytelnianie -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Dwuskładnikowe uwierzytelnianie</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Zwiększ bezpieczeństwo swojego konta, włączając uwierzytelnianie dwuskładnikowe.
                </p>

                <div class="mt-3">
                    @if(false) {{-- Tutaj warunek sprawdzający czy 2FA jest włączone --}}
                        <div class="flex items-center bg-green-50 p-3 rounded-md text-green-800">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Uwierzytelnianie dwuskładnikowe jest włączone.
                        </div>
                        <div class="mt-3">
                            <x-danger-button>
                                {{ __('Wyłącz uwierzytelnianie dwuskładnikowe') }}
                            </x-danger-button>
                        </div>
                    @else
                        <div class="flex items-center bg-yellow-50 p-3 rounded-md text-yellow-800">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Uwierzytelnianie dwuskładnikowe jest wyłączone.
                        </div>
                        <div class="mt-3">
                            <x-primary-button>
                                {{ __('Włącz uwierzytelnianie dwuskładnikowe') }}
                            </x-primary-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sekcja 6: Usuwanie konta -->
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">
                Usuń konto
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Po usunięciu konta wszystkie jego zasoby i dane zostaną trwale usunięte.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                @csrf
                @method('delete')

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-danger-button>
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        highlightActiveSection();
    });
    
    window.addEventListener('hashchange', function() {
        highlightActiveSection();
    });
    
    function highlightActiveSection() {
        const hash = window.location.hash || '#dane-uzytkownika';
        
        // Usuń podświetlenie ze wszystkich przycisków menu
        document.querySelectorAll('.space-y-6 nav a').forEach(link => {
            link.classList.remove('bg-blue-700');
            link.classList.add('bg-blue-600');
        });
        
        // Podświetl aktywny przycisk
        const activeLink = document.querySelector(`.space-y-6 nav a[href="${hash}"]`);
        if (activeLink) {
            activeLink.classList.remove('bg-blue-600');
            activeLink.classList.add('bg-blue-700');
        }
    }
</script>
@endsection 