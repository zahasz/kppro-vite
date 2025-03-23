<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-between">
            <div>
                <x-filament::button wire:click="clearCache" color="warning" icon="heroicon-o-trash">
                    Wyczyść pamięć podręczną
                </x-filament::button>
            </div>
            <x-filament::button type="submit" color="primary">
                Zapisz ustawienia
            </x-filament::button>
        </div>
    </x-filament-panels::form>
    
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">Informacje o systemie</x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Wersja PHP</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ phpversion() }}</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Wersja Laravel</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ app()->version() }}</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Środowisko</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ app()->environment() }}</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page> 