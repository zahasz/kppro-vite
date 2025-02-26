<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Dane osobowe -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Dane osobowe') }}</h2>
            
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Imię i nazwisko')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Hasło')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Potwierdź hasło')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Dane firmy -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Dane firmy') }}</h2>

            <!-- Company Name -->
            <div>
                <x-input-label for="company_name" :value="__('Nazwa firmy')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <!-- Tax Number -->
            <div class="mt-4">
                <x-input-label for="tax_number" :value="__('NIP')" />
                <x-text-input id="tax_number" class="block mt-1 w-full" type="text" name="tax_number" :value="old('tax_number')" />
                <x-input-error :messages="$errors->get('tax_number')" class="mt-2" />
            </div>

            <!-- REGON -->
            <div class="mt-4">
                <x-input-label for="regon" :value="__('REGON')" />
                <x-text-input id="regon" class="block mt-1 w-full" type="text" name="regon" :value="old('regon')" />
                <x-input-error :messages="$errors->get('regon')" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="mt-4">
                <x-input-label for="street" :value="__('Ulica')" />
                <x-text-input id="street" class="block mt-1 w-full" type="text" name="street" :value="old('street')" />
                <x-input-error :messages="$errors->get('street')" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <x-input-label for="postal_code" :value="__('Kod pocztowy')" />
                    <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" />
                    <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="city" :value="__('Miasto')" />
                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
            </div>

            <!-- Contact -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <x-input-label for="phone" :value="__('Telefon')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_email" :value="__('Email firmowy')" />
                    <x-text-input id="company_email" class="block mt-1 w-full" type="email" name="company_email" :value="old('company_email')" />
                    <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                </div>
            </div>

            <!-- Website -->
            <div class="mt-4">
                <x-input-label for="website" :value="__('Strona www')" />
                <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" />
                <x-input-error :messages="$errors->get('website')" class="mt-2" />
            </div>

            <!-- Bank Details -->
            <div class="mt-4">
                <x-input-label for="bank_name" :value="__('Nazwa banku')" />
                <x-text-input id="bank_name" class="block mt-1 w-full" type="text" name="bank_name" :value="old('bank_name')" />
                <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="bank_account" :value="__('Numer konta')" />
                <x-text-input id="bank_account" class="block mt-1 w-full" type="text" name="bank_account" :value="old('bank_account')" />
                <x-input-error :messages="$errors->get('bank_account')" class="mt-2" />
            </div>

            <!-- Logo -->
            <div class="mt-4">
                <x-input-label for="logo" :value="__('Logo firmy')" />
                <input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Masz już konto?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Zarejestruj się') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
