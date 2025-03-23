<x-filament-panels::page>
    <x-filament-panels::form wire:submit="importUsers">
        {{ $this->form }}
        
        @if($errors->has('importErrors'))
            <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 text-sm text-red-600 dark:text-red-400 rounded-lg">
                @foreach($errors->get('importErrors') as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button 
                type="submit" 
                color="primary"
            >
                Importuj użytkowników
            </x-filament::button>
            
            <x-filament::button 
                wire:click="cancelImport" 
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
            <x-slot name="heading">Instrukcje importu</x-slot>
            
            <div class="prose dark:prose-invert max-w-none">
                <p>Aby zaimportować użytkowników, przygotuj plik CSV z następującymi kolumnami:</p>
                
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kolumna</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Wymagana</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Opis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-2 text-sm">name</td>
                            <td class="px-3 py-2 text-sm">Tak</td>
                            <td class="px-3 py-2 text-sm">Nazwa użytkownika</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">first_name</td>
                            <td class="px-3 py-2 text-sm">Nie</td>
                            <td class="px-3 py-2 text-sm">Imię</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">last_name</td>
                            <td class="px-3 py-2 text-sm">Nie</td>
                            <td class="px-3 py-2 text-sm">Nazwisko</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">email</td>
                            <td class="px-3 py-2 text-sm">Tak</td>
                            <td class="px-3 py-2 text-sm">Adres e-mail (musi być unikalny)</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">password</td>
                            <td class="px-3 py-2 text-sm">Tak</td>
                            <td class="px-3 py-2 text-sm">Hasło (niezaszyfrowane)</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">position</td>
                            <td class="px-3 py-2 text-sm">Nie</td>
                            <td class="px-3 py-2 text-sm">Stanowisko</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 text-sm">phone</td>
                            <td class="px-3 py-2 text-sm">Nie</td>
                            <td class="px-3 py-2 text-sm">Numer telefonu</td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="mt-4">Przykładowy plik CSV:</p>
                <pre class="bg-gray-100 dark:bg-gray-800 p-2 rounded">name,first_name,last_name,email,password,position,phone
Jane Doe,Jane,Doe,jane.doe@example.com,StrongPassword123,Manager,123-456-7890
John Smith,John,Smith,john.smith@example.com,AnotherPassword456,Developer,098-765-4321</pre>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page> 