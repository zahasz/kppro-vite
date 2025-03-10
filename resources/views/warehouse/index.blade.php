<x-app-layout>
    <div class="py-6">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <div class="flex flex-col gap-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Magazyn</h1>
                    <p class="text-sm text-gray-600">Zarządzanie magazynem</p>
                </div>

                <!-- Kafelki -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Magazyn materiałów -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h2 class="ml-4 text-xl font-semibold text-gray-800">Magazyn materiałów</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Zarządzaj materiałami budowlanymi i wykończeniowymi</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Liczba pozycji: 0</span>
                                <a href="{{ route('warehouse.materials.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Przejdź
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Magazyn sprzętu -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                    </svg>
                                </div>
                                <h2 class="ml-4 text-xl font-semibold text-gray-800">Magazyn sprzętu</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Zarządzaj sprzętem budowlanym i maszynami</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Liczba pozycji: 0</span>
                                <a href="{{ route('warehouse.equipment.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Przejdź
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Magazyn narzędzi -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-yellow-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h2 class="ml-4 text-xl font-semibold text-gray-800">Magazyn narzędzi</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Zarządzaj narzędziami i drobnymi urządzeniami</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Liczba pozycji: 0</span>
                                <a href="{{ route('warehouse.tools.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Przejdź
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Garaż -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-purple-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h2 class="ml-4 text-xl font-semibold text-gray-800">Garaż</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Zarządzaj pojazdami i sprzętem transportowym</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Liczba pozycji: 0</span>
                                <a href="{{ route('warehouse.garage.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Przejdź
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statystyki -->
                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statystyki magazynu</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Całkowita wartość magazynu</p>
                                    <p class="text-2xl font-semibold text-gray-800">0,00 zł</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Liczba pozycji na stanie</p>
                                    <p class="text-2xl font-semibold text-gray-800">0</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Wartość materiałów</p>
                                    <p class="text-2xl font-semibold text-gray-800">0,00 zł</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Wartość sprzętu</p>
                                    <p class="text-2xl font-semibold text-gray-800">0,00 zł</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 