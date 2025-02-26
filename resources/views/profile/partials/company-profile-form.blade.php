<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profil firmy') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Zaktualizuj informacje o swojej firmie.') }}
        </p>
    </header>

    <form method="post" action="{{ route('company-profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nazwa firmy -->
            <div>
                <x-input-label for="company_name" :value="__('Nazwa firmy')" />
                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->companyProfile?->company_name)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>

            <!-- NIP -->
            <div>
                <x-input-label for="tax_number" :value="__('NIP')" />
                <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full" :value="old('tax_number', $user->companyProfile?->tax_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
            </div>

            <!-- REGON -->
            <div>
                <x-input-label for="regon" :value="__('REGON')" />
                <x-text-input id="regon" name="regon" type="text" class="mt-1 block w-full" :value="old('regon', $user->companyProfile?->regon)" />
                <x-input-error class="mt-2" :messages="$errors->get('regon')" />
            </div>

            <!-- Ulica -->
            <div>
                <x-input-label for="street" :value="__('Ulica')" />
                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $user->companyProfile?->street)" />
                <x-input-error class="mt-2" :messages="$errors->get('street')" />
            </div>

            <!-- Miasto -->
            <div>
                <x-input-label for="city" :value="__('Miasto')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->companyProfile?->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <!-- Kod pocztowy -->
            <div>
                <x-input-label for="postal_code" :value="__('Kod pocztowy')" />
                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $user->companyProfile?->postal_code)" />
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>

            <!-- Telefon -->
            <div>
                <x-input-label for="phone" :value="__('Telefon')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->companyProfile?->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->companyProfile?->email)" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <!-- Strona www -->
            <div>
                <x-input-label for="website" :value="__('Strona www')" />
                <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $user->companyProfile?->website)" />
                <x-input-error class="mt-2" :messages="$errors->get('website')" />
            </div>

            <!-- Nazwa banku -->
            <div>
                <x-input-label for="bank_name" :value="__('Nazwa banku')" />
                <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $user->companyProfile?->bank_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
            </div>

            <!-- Numer konta -->
            <div>
                <x-input-label for="bank_account" :value="__('Numer konta')" />
                <x-text-input id="bank_account" name="bank_account" type="text" class="mt-1 block w-full" :value="old('bank_account', $user->companyProfile?->bank_account)" />
                <x-input-error class="mt-2" :messages="$errors->get('bank_account')" />
            </div>

            <!-- Logo -->
            <div>
                <x-input-label for="logo" :value="__('Logo firmy')" />
                <input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('logo')" />
                @if($user->companyProfile?->logo_path)
                    <div class="mt-2">
                        <img src="{{ Storage::url($user->companyProfile->logo_path) }}" alt="Logo firmy" class="h-20">
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Zapisz') }}</x-primary-button>

            @if (session('status') === 'company-profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">
                    {{ __('Zapisano.') }}
                </p>
            @endif
        </div>
    </form>
</section> 