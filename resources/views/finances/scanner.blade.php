<x-admin-layout>
    <x-slot name="header">
        {{ __('Skaner dokumentów') }}
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Skaner dokumentów') }}</h2>
    </div>

    <!-- Obszar skanowania -->
    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-center">
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-blue-100 bg-opacity-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-upload text-blue-600 text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-800">Przeciągnij i upuść pliki</h3>
                <p class="text-sm text-gray-600">lub</p>
            </div>
            
            <label class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                <span>Wybierz plik</span>
                <input type="file" class="hidden" accept="image/*,.pdf" multiple>
            </label>

            <p class="mt-2 text-xs text-gray-600">Obsługiwane formaty: JPG, PNG, PDF</p>
        </div>
    </div>

    <!-- Lista ostatnio zeskanowanych dokumentów -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ostatnio zeskanowane</h3>
        <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Przykładowy dokument -->
                    <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 bg-opacity-10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">Faktura VAT 123/2024</h4>
                                <p class="text-xs text-gray-600">Zeskanowano: 22.02.2024</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 