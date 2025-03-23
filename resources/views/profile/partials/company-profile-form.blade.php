<section>
    @if (session('status') === 'company-profile-updated')
        <div class="mb-4 font-medium text-sm text-green-600">
            Profil firmy został zaktualizowany pomyślnie.
        </div>
    @endif

    @if(!$user->companyProfile)
        <div class="mt-3 mb-4">
            <a href="{{ route('company-profile.create-test') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Utwórz przykładowy profil firmy
            </a>
        </div>
    @endif

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
                    <x-input-label for="company_phone" :value="__('Telefon')" />
                    <x-text-input id="company_phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->companyProfile?->phone)" required />
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
                    <x-input-label for="company_email" :value="__('Email')" />
                    <x-text-input id="company_email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->companyProfile?->email)" required />
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
            <button type="submit" id="save-company-profile-button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Zapisz dane firmy') }}
            </button>

            <p id="save-notification" 
               class="text-sm text-green-600 transition-opacity duration-500 opacity-0 hidden">
                {{ __('Zapisano pomyślnie!') }}
            </p>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Znajdź przycisk zapisywania profilu firmy
    const saveButton = document.getElementById('save-company-profile-button');
    if (saveButton) {
        // Znajdź formularz, w którym znajduje się przycisk
        const form = saveButton.closest('form');
        
        // Dodaj obsługę zdarzenia kliknięcia
        saveButton.addEventListener('click', function(e) {
            console.log('Przycisk zapisz kliknięty, wysyłam formularz profilu firmy');
            // NIE blokujemy domyślnej akcji przycisku typu submit - formularze HTML będą działać normalnie
        });
    } else {
        console.error('Nie znaleziono przycisku zapisz profilu firmy');
    }
    
    // Sprawdź czy jest tekst w stopce po załadowaniu strony
    const footerTextarea = document.getElementById('invoice_footer');
    if (footerTextarea) {
        console.log('Wartość stopki faktury:', footerTextarea.value);
    }
});
</script> 