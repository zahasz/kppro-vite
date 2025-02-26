@extends('layouts.app')

@section('title', 'Profil użytkownika')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Profil użytkownika</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ $profile->id ? route('profile.update', $profile) : route('profile.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="bg-white rounded-lg shadow-sm p-6">
                @csrf
                @if($profile->id)
                    @method('PUT')
                @endif

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Typ profilu</h2>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="type" value="individual" class="mr-2" 
                                {{ old('type', $profile->type) === 'individual' ? 'checked' : '' }}
                                onchange="toggleProfileFields(this.value)">
                            Osoba prywatna
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="company" class="mr-2"
                                {{ old('type', $profile->type) === 'company' ? 'checked' : '' }}
                                onchange="toggleProfileFields(this.value)">
                            Firma
                        </label>
                    </div>
                </div>

                <div id="individual-fields" class="{{ old('type', $profile->type) === 'company' ? 'hidden' : '' }}">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block mb-2">Imię</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $profile->first_name) }}" 
                                   class="form-input w-full">
                            @error('first_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2">Nazwisko</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $profile->last_name) }}" 
                                   class="form-input w-full">
                            @error('last_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="company-fields" class="{{ old('type', $profile->type) === 'individual' ? 'hidden' : '' }}">
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block mb-2">Nazwa firmy</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}" 
                                   class="form-input w-full">
                            @error('company_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-2">NIP</label>
                                <input type="text" name="tax_number" value="{{ old('tax_number', $profile->tax_number) }}" 
                                       class="form-input w-full">
                                @error('tax_number')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-2">REGON</label>
                                <input type="text" name="regon" value="{{ old('regon', $profile->regon) }}" 
                                       class="form-input w-full">
                            </div>
                            <div>
                                <label class="block mb-2">KRS</label>
                                <input type="text" name="krs" value="{{ old('krs', $profile->krs) }}" 
                                       class="form-input w-full">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Dane kontaktowe</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $profile->email) }}" 
                                   class="form-input w-full">
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2">Telefon</label>
                            <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" 
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label class="block mb-2">Strona www</label>
                            <input type="url" name="website" value="{{ old('website', $profile->website) }}" 
                                   class="form-input w-full">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Adres</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block mb-2">Ulica</label>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-2">
                                    <input type="text" name="street" value="{{ old('street', $profile->street) }}" 
                                           class="form-input w-full" placeholder="Nazwa ulicy">
                                </div>
                                <div>
                                    <input type="text" name="street_number" value="{{ old('street_number', $profile->street_number) }}" 
                                           class="form-input w-full" placeholder="Nr">
                                </div>
                                <div>
                                    <input type="text" name="apartment_number" value="{{ old('apartment_number', $profile->apartment_number) }}" 
                                           class="form-input w-full" placeholder="Lok.">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2">Kod pocztowy</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" 
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label class="block mb-2">Miejscowość</label>
                            <input type="text" name="city" value="{{ old('city', $profile->city) }}" 
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label class="block mb-2">Kraj</label>
                            <input type="text" name="country" value="{{ old('country', $profile->country) }}" 
                                   class="form-input w-full">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Dane bankowe</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Nazwa banku</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" 
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label class="block mb-2">Numer konta</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number) }}" 
                                   class="form-input w-full">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Ustawienia faktur</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Domyślna metoda płatności</label>
                            <select name="default_payment_method" class="form-select w-full">
                                <option value="przelew" {{ old('default_payment_method', $profile->default_payment_method) === 'przelew' ? 'selected' : '' }}>
                                    Przelew bankowy
                                </option>
                                <option value="gotowka" {{ old('default_payment_method', $profile->default_payment_method) === 'gotowka' ? 'selected' : '' }}>
                                    Gotówka
                                </option>
                                <option value="karta" {{ old('default_payment_method', $profile->default_payment_method) === 'karta' ? 'selected' : '' }}>
                                    Karta płatnicza
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2">Domyślny termin płatności (dni)</label>
                            <input type="number" name="default_payment_deadline_days" 
                                   value="{{ old('default_payment_deadline_days', $profile->default_payment_deadline_days) }}" 
                                   class="form-input w-full">
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2">Dodatkowe uwagi na fakturze</label>
                            <textarea name="invoice_notes" class="form-textarea w-full" rows="3">{{ old('invoice_notes', $profile->invoice_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Logo</h2>
                    <div class="space-y-4">
                        @if($profile->logo_path)
                            <div class="w-48 h-48 relative">
                                <img src="{{ Storage::url($profile->logo_path) }}" alt="Logo" class="object-contain w-full h-full">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-input">
                        @error('logo')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        {{ $profile->id ? 'Zapisz zmiany' : 'Utwórz profil' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleProfileFields(type) {
    const individualFields = document.getElementById('individual-fields');
    const companyFields = document.getElementById('company-fields');
    
    if (type === 'individual') {
        individualFields.classList.remove('hidden');
        companyFields.classList.add('hidden');
    } else {
        individualFields.classList.add('hidden');
        companyFields.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection 