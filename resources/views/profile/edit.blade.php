@extends('layouts.app')

@section('title', 'Edycja profilu')

@section('content')
<div class="space-y-6">
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">
                Informacje o profilu
            </h2>

            @if (session('status') === 'profile-updated')
                <div class="mb-4 font-medium text-sm text-green-600">
                    Profil został zaktualizowany pomyślnie.
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img class="h-16 w-16 object-cover rounded-full" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Zdjęcie profilowe
                        </label>
                        <input type="file" name="avatar" class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                    </div>
                </div>

                <div>
                    <x-input-label for="username" value="Nazwa użytkownika" />
                    <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" autofocus autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="first_name" value="Imię" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" autocomplete="given-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                    </div>

                    <div>
                        <x-input-label for="last_name" value="Nazwisko" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" autocomplete="family-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" autocomplete="email" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Telefon" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="position" value="Stanowisko" />
                    <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" />
                    <x-input-error class="mt-2" :messages="$errors->get('position')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Zapisz') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div>
            @include('profile.partials.company-profile-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">
                Usuń konto
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Po usunięciu konta wszystkie jego zasoby i dane zostaną trwale usunięte.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                @csrf
                @method('delete')

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-danger-button>
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
