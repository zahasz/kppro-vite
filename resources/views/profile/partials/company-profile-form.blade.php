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

        <!-- Dane podstawowe -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('Dane podstawowe') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nazwa firmy -->
                <div>
                    <x-input-label for="company_name" :value="__('Nazwa firmy')" />
                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->companyProfile?->company_name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                </div>

                <!-- Forma prawna -->
                <div>
                    <x-input-label for="legal_form" :value="__('Forma prawna')" />
                    <select id="legal_form" name="legal_form" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Wybierz formę prawną</option>
                        <option value="sole_proprietorship" {{ old('legal_form', $user->companyProfile?->legal_form) === 'sole_proprietorship' ? 'selected' : '' }}>Jednoosobowa działalność gospodarcza</option>
                        <option value="partnership" {{ old('legal_form', $user->companyProfile?->legal_form) === 'partnership' ? 'selected' : '' }}>Spółka cywilna</option>
                        <option value="limited_partnership" {{ old('legal_form', $user->companyProfile?->legal_form) === 'limited_partnership' ? 'selected' : '' }}>Spółka komandytowa</option>
                        <option value="limited_liability" {{ old('legal_form', $user->companyProfile?->legal_form) === 'limited_liability' ? 'selected' : '' }}>Spółka z o.o.</option>
                        <option value="joint_stock" {{ old('legal_form', $user->companyProfile?->legal_form) === 'joint_stock' ? 'selected' : '' }}>Spółka akcyjna</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('legal_form')" />
                </div>

                <!-- NIP -->
                <div>
                    <x-input-label for="tax_number" :value="__('NIP')" />
                    <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full" :value="old('tax_number', $user->companyProfile?->tax_number)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
                </div>

                <!-- REGON -->
                <div>
                    <x-input-label for="regon" :value="__('REGON')" />
                    <x-text-input id="regon" name="regon" type="text" class="mt-1 block w-full" :value="old('regon', $user->companyProfile?->regon)" />
                    <x-input-error class="mt-2" :messages="$errors->get('regon')" />
                </div>

                <!-- KRS -->
                <div>
                    <x-input-label for="krs" :value="__('KRS')" />
                    <x-text-input id="krs" name="krs" type="text" class="mt-1 block w-full" :value="old('krs', $user->companyProfile?->krs)" />
                    <x-input-error class="mt-2" :messages="$errors->get('krs')" />
                </div>
            </div>
        </div>

        <!-- Dane adresowe -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('Dane adresowe') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ulica -->
                <div>
                    <x-input-label for="street" :value="__('Ulica i numer')" />
                    <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $user->companyProfile?->street)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('street')" />
                </div>

                <!-- Kod pocztowy -->
                <div>
                    <x-input-label for="postal_code" :value="__('Kod pocztowy')" />
                    <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $user->companyProfile?->postal_code)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                </div>

                <!-- Miasto -->
                <div>
                    <x-input-label for="city" :value="__('Miasto')" />
                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->companyProfile?->city)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>

                <!-- Województwo -->
                <div>
                    <x-input-label for="state" :value="__('Województwo')" />
                    <select id="state" name="state" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Wybierz województwo</option>
                        @foreach(['dolnośląskie', 'kujawsko-pomorskie', 'lubelskie', 'lubuskie', 'łódzkie', 'małopolskie', 'mazowieckie', 'opolskie', 'podkarpackie', 'podlaskie', 'pomorskie', 'śląskie', 'świętokrzyskie', 'warmińsko-mazurskie', 'wielkopolskie', 'zachodniopomorskie'] as $state)
                            <option value="{{ $state }}" {{ old('state', $user->companyProfile?->state) === $state ? 'selected' : '' }}>{{ ucfirst($state) }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                </div>

                <!-- Kraj -->
                <div>
                    <x-input-label for="country" :value="__('Kraj')" />
                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->companyProfile?->country ?? 'Polska')" required />
                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                </div>
            </div>
        </div>

        <!-- Dane kontaktowe -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('Dane kontaktowe') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Telefon -->
                <div>
                    <x-input-label for="phone" :value="__('Telefon')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->companyProfile?->phone)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <!-- Telefon dodatkowy -->
                <div>
                    <x-input-label for="phone_additional" :value="__('Telefon dodatkowy')" />
                    <x-text-input id="phone_additional" name="phone_additional" type="tel" class="mt-1 block w-full" :value="old('phone_additional', $user->companyProfile?->phone_additional)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone_additional')" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->companyProfile?->email)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Email dodatkowy -->
                <div>
                    <x-input-label for="email_additional" :value="__('Email dodatkowy')" />
                    <x-text-input id="email_additional" name="email_additional" type="email" class="mt-1 block w-full" :value="old('email_additional', $user->companyProfile?->email_additional)" />
                    <x-input-error class="mt-2" :messages="$errors->get('email_additional')" />
                </div>

                <!-- Strona www -->
                <div>
                    <x-input-label for="website" :value="__('Strona www')" />
                    <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $user->companyProfile?->website)" />
                    <x-input-error class="mt-2" :messages="$errors->get('website')" />
                </div>
            </div>
        </div>

        <!-- Dane bankowe -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('Dane bankowe') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nazwa banku -->
                <div>
                    <x-input-label for="bank_name" :value="__('Nazwa banku')" />
                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $user->companyProfile?->bank_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                </div>

                <!-- Numer konta -->
                <div>
                    <x-input-label for="bank_account" :value="__('Numer konta')" />
                    <x-text-input id="bank_account" name="bank_account" type="text" class="mt-1 block w-full" :value="old('bank_account', $user->companyProfile?->bank_account)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('bank_account')" />
                </div>

                <!-- SWIFT -->
                <div>
                    <x-input-label for="swift" :value="__('Kod SWIFT')" />
                    <x-text-input id="swift" name="swift" type="text" class="mt-1 block w-full" :value="old('swift', $user->companyProfile?->swift)" />
                    <x-input-error class="mt-2" :messages="$errors->get('swift')" />
                </div>
            </div>
        </div>

        <!-- Dodatkowe informacje -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('Dodatkowe informacje') }}</h3>
            <div class="grid grid-cols-1 gap-6">
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

                <!-- Notatki -->
                <div>
                    <x-input-label for="notes" :value="__('Notatki')" />
                    <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('notes', $user->companyProfile?->notes) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>
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