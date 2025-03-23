<x-admin-layout>
    <x-slot name="header">
        Szczegóły roli
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Rola: {{ $role->name }}</h2>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Edytuj
                            </a>
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę rolę?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Usuń
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-gray-700">Informacje podstawowe</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">ID</p>
                                    <p class="mt-1">{{ $role->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nazwa</p>
                                    <p class="mt-1">{{ $role->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Guard</p>
                                    <p class="mt-1">{{ $role->guard_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Data utworzenia</p>
                                    <p class="mt-1">{{ $role->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-gray-700">Uprawnienia ({{ $role->permissions->count() }})</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @if($role->permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($role->permissions as $permission)
                                        <div class="bg-white p-2 rounded border border-gray-200">
                                            <p class="text-sm">{{ $permission->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Ta rola nie ma przypisanych uprawnień.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-gray-700">Użytkownicy z tą rolą ({{ $role->users->count() }})</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @if($role->users->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imię i nazwisko</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($role->users as $user)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">Szczegóły</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Brak użytkowników z przypisaną tą rolą.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Powrót do listy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 