<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Historia logowań użytkownika {{ $user->name }}
        </x-slot>

        <x-slot name="description">
            Lista wszystkich logowań do systemu.
        </x-slot>

        <div class="space-y-4">
            @if($user->last_login_at)
                <div class="p-4 rounded-lg bg-primary-50 dark:bg-primary-950 border border-primary-200 dark:border-primary-800">
                    <h3 class="font-medium text-primary-600 dark:text-primary-400">Ostatnie logowanie</h3>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Data i czas:</span>
                            <p class="font-medium">{{ $user->last_login_at->format('d.m.Y H:i:s') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Adres IP:</span>
                            <p class="font-medium">{{ $user->last_login_ip ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Status konta:</span>
                            <p class="font-medium">
                                @if($user->is_active)
                                    <span class="text-success-600 dark:text-success-400">Aktywne</span>
                                @else
                                    <span class="text-danger-600 dark:text-danger-400">Nieaktywne</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <x-filament::table>
                <x-slot name="header">
                    <x-filament::table.header-cell>
                        Data
                    </x-filament::table.header-cell>
                    <x-filament::table.header-cell>
                        Adres IP
                    </x-filament::table.header-cell>
                    <x-filament::table.header-cell>
                        Przeglądarka
                    </x-filament::table.header-cell>
                    <x-filament::table.header-cell>
                        Status
                    </x-filament::table.header-cell>
                </x-slot>

                <x-filament::table.body>
                    @if($user->last_login_at)
                        <x-filament::table.row>
                            <x-filament::table.cell>
                                {{ $user->last_login_at->format('d.m.Y H:i:s') }}
                            </x-filament::table.cell>
                            <x-filament::table.cell>
                                {{ $user->last_login_ip ?? 'Brak danych' }}
                            </x-filament::table.cell>
                            <x-filament::table.cell>
                                Brak danych
                            </x-filament::table.cell>
                            <x-filament::table.cell>
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-medium bg-success-500 text-white">
                                    Sukces
                                </span>
                            </x-filament::table.cell>
                        </x-filament::table.row>
                    @else
                        <x-filament::table.row>
                            <x-filament::table.cell colspan="4" class="text-center py-4">
                                <span class="text-gray-500 dark:text-gray-400">Brak historii logowań dla tego użytkownika</span>
                            </x-filament::table.cell>
                        </x-filament::table.row>
                    @endif
                </x-filament::table.body>
            </x-filament::table>

            <div class="flex justify-end mt-4">
                <x-filament::button color="gray" tag="a" href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}">
                    Powrót do listy użytkowników
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page> 