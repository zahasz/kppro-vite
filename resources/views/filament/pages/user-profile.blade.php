<x-filament-panels::page>
    <x-filament::grid :default="1" class="mt-6 gap-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Mój profil') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Aktualizuj swoje dane osobowe i preferencje') }}
            </x-slot>

            <form wire:submit="updateProfile" class="space-y-6">
                {{ $this->userProfileForm }}

                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        {{ __('Zapisz zmiany') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                {{ __('Hasło') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Zmień swoje hasło, aby zabezpieczyć konto') }}
            </x-slot>

            <form wire:submit="updatePassword" class="space-y-6">
                {{ $this->passwordForm }}

                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        {{ __('Zmień hasło') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        @if(auth()->user()->last_login_at)
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Informacje o logowaniu') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Ostatnia aktywność na koncie') }}
                </x-slot>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Ostatnie logowanie:') }}</span>
                        <span>{{ auth()->user()->last_login_at->format('d.m.Y H:i') }}</span>
                    </div>

                    @if(auth()->user()->last_login_ip)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Adres IP:') }}</span>
                            <span>{{ auth()->user()->last_login_ip }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Liczba logowań:') }}</span>
                        <span>{{ auth()->user()->login_count ?? 1 }}</span>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </x-filament::grid>
</x-filament-panels::page> 