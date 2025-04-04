<x-app-layout>
    <div class="py-6">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <div class="flex flex-col gap-6">
                <!-- Nagłówek -->
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Magazyn materiałów</h1>
                        <p class="text-sm text-gray-600">Zarządzaj materiałami budowlanymi i wykończeniowymi</p>
                    </div>
                    <a href="{{ route('warehouse.materials.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Dodaj materiał
                    </a>
                </div>

                <!-- Filtry i wyszukiwanie -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <form action="{{ route('warehouse.materials.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Szukaj</label>
                                <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Nazwa, kod, kategoria...">
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Wszystkie kategorie</option>
                                    <option value="building">Materiały budowlane</option>
                                    <option value="finishing">Materiały wykończeniowe</option>
                                    <option value="installation">Materiały instalacyjne</option>
                                    <option value="other">Inne</option>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Wszystkie statusy</option>
                                    <option value="available">Dostępne</option>
                                    <option value="low">Niski stan</option>
                                    <option value="out">Brak na stanie</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filtruj
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista materiałów -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoria</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jednostka</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena jedn.</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Akcje</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Przykładowy wiersz -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">MAT001</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cement portlandzki</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Materiały budowlane</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Dostępny
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">kg</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0,85 zł</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">850,00 zł</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 