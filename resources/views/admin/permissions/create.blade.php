<x-admin-layout>
    <x-slot name="header">
        Dodaj nowe uprawnienie
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Tworzenie uprawnienia</h2>

                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa uprawnienia</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="np. create-posts">
                            <p class="mt-1 text-sm text-gray-500">Nazwa powinna być unikalna i opisywać funkcjonalność, np. "create-posts", "edit-settings", "manage-users".</p>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-1">Guard</label>
                            <select name="guard_name" id="guard_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>api</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Strażnik, dla którego uprawnienie będzie dostępne.</p>
                            @error('guard_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Anuluj
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Dodaj uprawnienie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 