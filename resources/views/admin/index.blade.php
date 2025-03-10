<x-app-layout>
    <div class="py-6" data-section="admin">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <div class="flex flex-col gap-6">
                <!-- Nagłówek -->
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Panel administratora</h1>
                    <p class="text-sm text-gray-600">Zarządzaj systemem i użytkownikami</p>
                </div>

                <!-- Karty statystyk -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Użytkownicy</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['users_count'] }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Role</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['roles_count'] }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Uprawnienia</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['permissions_count'] }}</div>
                    </div>
                </div>

                <!-- Menu zarządzania -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('admin.roles') }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Role</h3>
                                <p class="text-sm text-gray-500">Zarządzaj rolami użytkowników</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Uprawnienia</h3>
                                <p class="text-sm text-gray-500">Zarządzaj uprawnieniami systemu</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Ustawienia</h3>
                                <p class="text-sm text-gray-500">Konfiguruj ustawienia systemu</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 