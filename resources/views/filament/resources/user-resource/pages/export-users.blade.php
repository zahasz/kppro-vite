<x-filament-panels::page>
    <x-filament-panels::form wire:submit="exportUsers">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button 
                type="submit" 
                color="primary"
            >
                Eksportuj użytkowników
            </x-filament::button>
            
            <x-filament::button 
                wire:click="cancelExport" 
                color="gray"
                tag="a"
                href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}"
            >
                Anuluj
            </x-filament::button>
        </div>
    </x-filament-panels::form>
    
    <div class="mt-8">
        <x-filament::section>
            <x-slot name="heading">Informacje o eksporcie</x-slot>
            
            <div class="prose dark:prose-invert max-w-none">
                <p>Wybierz format i opcje eksportu danych użytkowników:</p>
                
                <ul>
                    <li><strong>CSV</strong> - standardowy format tekstowy, który można otworzyć w Excel, Google Sheets lub innych programach do arkuszy kalkulacyjnych.</li>
                    <li><strong>Excel (XLSX)</strong> - format Microsoft Excel, który zachowuje formatowanie i umożliwia łatwą pracę z danymi.</li>
                    <li><strong>PDF</strong> - format dokumentów przenośnych, idealny do drukowania lub udostępniania danych w formie niemodyfikowalnej.</li>
                </ul>
                
                <p><strong>Uwaga:</strong> Dane eksportowane do pliku nie zawierają haseł użytkowników. Hasła są przechowywane w postaci zaszyfrowanej i nie mogą być odzyskane.</p>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page> 