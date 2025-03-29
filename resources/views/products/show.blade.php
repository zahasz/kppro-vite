<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Szczegóły produktu') }}
            </h2>
            <div>
                <a href="{{ route('products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    {{ __('Edytuj') }}
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Czy na pewno chcesz usunąć ten produkt?')">
                        {{ __('Usuń') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">{{ __('Informacje podstawowe') }}</h3>
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Nazwa') }}:</span>
                                <span class="block mt-1">{{ $product->name }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Opis') }}:</span>
                                <p class="block mt-1">{{ $product->description ?: 'Brak opisu' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Status') }}:</span>
                                <span class="block mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->status === 'active' ? 'Aktywny' : 'Nieaktywny' }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">{{ __('Informacje cenowe') }}</h3>
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Jednostka') }}:</span>
                                <span class="block mt-1">{{ $product->unit }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Cena jednostkowa') }}:</span>
                                <span class="block mt-1">{{ number_format($product->unit_price, 2) }} zł</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Stawka VAT') }}:</span>
                                <span class="block mt-1">{{ $product->tax_rate }}%</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-gray-600 font-medium">{{ __('Cena brutto') }}:</span>
                                <span class="block mt-1">{{ number_format($product->unit_price * (1 + $product->tax_rate / 100), 2) }} zł</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t pt-4">
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-900">
                            &larr; {{ __('Wróć do listy produktów') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 