@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Dane podstawowe -->
    <div class="space-y-4">
        <h4 class="font-medium text-gray-900">Dane podstawowe</h4>
        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">Nazwa firmy *</label>
            <input type="text" name="company_name" id="company_name" required
                   value="{{ old('company_name') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
            <input type="text" name="nip" id="nip"
                   value="{{ old('nip') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="regon" class="block text-sm font-medium text-gray-700">REGON</label>
            <input type="text" name="regon" id="regon"
                   value="{{ old('regon') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
            <input type="text" name="phone" id="phone"
                   value="{{ old('phone') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
    </div>

    <!-- Adres -->
    <div class="space-y-4">
        <h4 class="font-medium text-gray-900">Adres</h4>
        <div>
            <label for="street" class="block text-sm font-medium text-gray-700">Ulica i numer</label>
            <input type="text" name="street" id="street"
                   value="{{ old('street') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
            <input type="text" name="postal_code" id="postal_code"
                   value="{{ old('postal_code') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">Miejscowość</label>
            <input type="text" name="city" id="city"
                   value="{{ old('city') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="country" class="block text-sm font-medium text-gray-700">Kraj</label>
            <input type="text" name="country" id="country"
                   value="{{ old('country', 'Polska') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
    </div>
</div>

<!-- Dane bankowe -->
<div class="space-y-4 mt-4">
    <h4 class="font-medium text-gray-900">Dane bankowe</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="bank_name" class="block text-sm font-medium text-gray-700">Nazwa banku</label>
            <input type="text" name="bank_name" id="bank_name"
                   value="{{ old('bank_name') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Numer konta</label>
            <input type="text" name="bank_account_number" id="bank_account_number"
                   value="{{ old('bank_account_number') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="swift_code" class="block text-sm font-medium text-gray-700">Kod SWIFT</label>
            <input type="text" name="swift_code" id="swift_code"
                   value="{{ old('swift_code') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
    </div>
</div>

<!-- Uwagi -->
<div class="space-y-4 mt-4">
    <h4 class="font-medium text-gray-900">Dodatkowe informacje</h4>
    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Uwagi</label>
        <textarea name="notes" id="notes" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
    </div>
</div> 