<x-admin-layout>
    <x-slot name="header">
        Zarządzanie uprawnieniami subskrypcji
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filtrowanie i wyszukiwanie -->
                    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="relative">
                            <form action="{{ route('admin.subscriptions.permissions') }}" method="GET" class="flex">
                                <div class="relative">
                                    <input type="text" name="search" id="permissionSearch" value="{{ request()->get('search') }}" class="pl-10 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Szukaj uprawnień..." />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="ml-2 px-4 py-2.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150">
                                    Filtruj
                                </button>
                            </form>
                        </div>
                        <a href="{{ route('admin.subscriptions.permissions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 text-center">
                            Dodaj nowe uprawnienie
                        </a>
                    </div>

                    <!-- Zakładki dla grup uprawnień -->
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="permissionTabs" role="tablist">
                            <li class="mr-2" role="presentation">
                                <a href="{{ route('admin.subscriptions.permissions', ['category' => '']) }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ !request()->has('category') ? 'border-indigo-600 active' : 'border-transparent hover:border-gray-300' }}">
                                    Wszystkie uprawnienia
                                </a>
                            </li>
                            <li class="mr-2" role="presentation">
                                <a href="{{ route('admin.subscriptions.permissions', ['category' => 'finance']) }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ request('category') == 'finance' ? 'border-indigo-600 active' : 'border-transparent hover:border-gray-300' }}">
                                    Finanse
                                </a>
                            </li>
                            <li class="mr-2" role="presentation">
                                <a href="{{ route('admin.subscriptions.permissions', ['category' => 'warehouse']) }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ request('category') == 'warehouse' ? 'border-indigo-600 active' : 'border-transparent hover:border-gray-300' }}">
                                    Magazyn
                                </a>
                            </li>
                            <li class="mr-2" role="presentation">
                                <a href="{{ route('admin.subscriptions.permissions', ['category' => 'documents']) }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ request('category') == 'documents' ? 'border-indigo-600 active' : 'border-transparent hover:border-gray-300' }}">
                                    Dokumenty
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="{{ route('admin.subscriptions.permissions', ['category' => 'users']) }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ request('category') == 'users' ? 'border-indigo-600 active' : 'border-transparent hover:border-gray-300' }}">
                                    Użytkownicy
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Lista uprawnień -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="permissionsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Uprawnienie
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kod
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategoria
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Plany
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Akcje
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $permission->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $permission->code }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $permission->category == 'finance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $permission->category == 'warehouse' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $permission->category == 'documents' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $permission->category == 'users' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $permission->category == 'reports' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $permission->category == 'integrations' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                                {{ $permission->category == 'other' ? 'bg-gray-100 text-gray-800' : '' }}
                                            ">
                                                {{ ucfirst($permission->category) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($permission->plans as $plan)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $plan->name }}
                                                    </span>
                                                @endforeach
                                                @if ($permission->plans->isEmpty())
                                                    <span class="text-xs text-gray-500">Brak przypisanych planów</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.subscriptions.permissions.edit', $permission->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                                            <form action="{{ route('admin.subscriptions.permissions.destroy', $permission->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć to uprawnienie?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Usuń</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Nie znaleziono żadnych uprawnień subskrypcji.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginacja -->
                    <div class="mt-4">
                        {{ $permissions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 