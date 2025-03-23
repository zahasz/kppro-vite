<x-admin-layout>
    <x-slot name="header">
        Zarządzanie użytkownikami
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900">Dodaj nowego użytkownika</h2>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <x-input-label for="name" :value="__('Nazwa użytkownika')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Adres e-mail')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Hasło')" />
                        <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Potwierdź hasło')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required />
                    </div>

                    <div class="flex items-center mt-4">
                        <input id="is_active" type="checkbox" name="is_active" value="1" checked
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-600">{{ __('Aktywny') }}</label>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Role użytkownika</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input id="role_{{ $role->id }}" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-600">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Uprawnienia użytkownika</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-h-60 overflow-y-auto">
                        @foreach($permissions as $permission)
                            <div class="flex items-center">
                                <input id="permission_{{ $permission->id }}" type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-600">{{ $permission->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">
                        Anuluj
                    </a>
                    <x-primary-button class="ml-3">
                        {{ __('Dodaj użytkownika') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout> 